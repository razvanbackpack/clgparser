<?php
namespace ClgView\Parser;

class ClgParser{
    private $lines;

    private $items = [];
    private $currentHeader = [];

    public function __construct(string $file_path)
    {
        $this->lines = file($file_path);
    }

    public function parse(): array {
        foreach($this->lines as $line) {
            $this->isHeader($line);
            $this->isListItem($line);
        }

        return $this->items;
    }
    #region Header Parser
    private function isHeader(string $line):void {
        if(preg_match("/^#{1,6}\s/", $line))
        {
            $this->parseHeader($line);
        }
    }

    private function parseHeader(string $line):void {
        $headerData = ['type' => 'header', 'level' => 0, 'text' => ''];

        $headerData['level'] = strspn($line, '#');
        $headerData['text'] = $this->formatInlineText(trim(substr(ltrim($line), $headerData['level'])));

        $this->items[] = $headerData;
        $this->currentHeader = $headerData;
    }
    #endregion Header Parser

    #region List Parser
    private function isListItem(string $line): void {
        if(preg_match("/^(\s*)([-*]|\d+\.)\s(.+)/", $line, $matches))
        {
            $this->parseListItem($matches);
        }
    }

    private function parseListItem(array $item):void {
        $itemData = ['type' => 'list', 'level' => 0, 'marker' => '', 'text' => ''];

        $itemData['level'] = strlen($item[1]);  // number of spaces
        $itemData['marker'] = $this->currentHeader['text'];           // -, *, or 1.
        $itemData['text'] = $this->formatInlineText($item[3]);

        $this->items[] = $itemData;
    }
    #endregion List Parser

    #region Inline Text Formatter
    private function formatInlineText(string $text): string {
        $text = htmlspecialchars($text);

        // Bold: **text**
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        // Italic: _text_
        $text = preg_replace('/_(.+?)_/', '<em>$1</em>', $text);
        // Underline: ++text++
        $text = preg_replace('/\+\+(.+?)\+\+/', '<u>$1</u>', $text);

        return $text;
    }
    #endregion Inline Text Formatter
}
