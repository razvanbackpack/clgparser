<?php
namespace ClgView;

class ClgOutput {
    private array $items = [];
    private array $options = [
        'space' => '',
        'subtitles_as_labels' => true,
        'item_ids' => [
            'title' => 'title_id',
            'subtitle' => 'subtitle_id',
            'marker' => 'marker_id',
            'list_container' => 'container_id',
            'list_item' => 'item_id',
        ],
        'item_classes' => [
            'title' => 'title_class',
            'subtitle' => 'subtitle_class',
            'marker' => 'marker_class',
            'list_container' => 'container_class',
            'list_item' => 'item_class',
        ],
    ];

    /**
     * @param $parsedItems array
     * @param $options array
     */
    public function __construct(array $parsedItems, array $options)
    {
        $this->items = $parsedItems;
        if ($this->validateOptions($options))
            $this->options = $options;
    }

    /**
     * @param $options array
     */
    private function validateOptions(array $options): bool {
        $missing_fields = [];
        $required_fields = ['space', 'subtitles_as_labels', 'item_ids', 'item_classes'];

        foreach ($required_fields as $field) {
            if (!isset($options[$field])) {
                $missing_fields[] = $field;
            }
        }

        if (!empty($missing_fields)) {
            throw new \InvalidArgumentException(
                'Missing required options: ' . implode(', ', $missing_fields)
            );
        }

        $nested_required = ['title', 'subtitle', 'marker', 'list_container', 'list_item'];
        $this->validateNestedOptions($options, 'item_ids', $nested_required, $missing_fields);
        $this->validateNestedOptions($options, 'item_classes', $nested_required, $missing_fields);

        if (!empty($missing_fields)) {
            throw new \InvalidArgumentException(
                'Missing required options: ' . implode(', ', $missing_fields)
            );
        }

        return true;
    }

    /**
     * @param $options array
     * @param $key string
     * @param $required_keys array
     * @param $missing_fields array
     */
    private function validateNestedOptions(array $options, string $key, array $required_keys, array &$missing_fields): void {
        if (!isset($options[$key]))
            return;

        foreach ($required_keys as $nested_key) {
            if (!isset($options[$key][$nested_key])) {
                $missing_fields[] = "{$key}[$nested_key]";
            }
        }
    }

    public function output(): void {

        $in_list = false;
        $title_count = 0;
        $list_count = 0;
        foreach($this->items as $item)
        {
            switch($item['type']) {
                case 'header':
                    $this->endListIfInList($in_list);
                    $this->handleTitleItem($item, $title_count);
                    break;
                case 'list':
                    $this->handleListItem($in_list, $item, $list_count);
                    break;
                default:
                    $this->endListIfInList($in_list);
                    break;
            }
        }

        $this->endListIfInList($in_list);
    }

    /**
     * @param $in_list bool
     */
    private function endListIfInList(&$in_list = false): void
    {
        if ($in_list) // out of list items, end list container
            echo '</div>';
        $in_list = false;
    }

    /**
     * @param $in_list bool
     * @param $item array
     * @param $loop_count int
     */
    private function handleListItem(bool &$in_list, array $item, int &$loop_count): void
    {
        if (!$in_list) // output list container
            '<div id ="' . $this->options['item_ids']['list_container'] . $loop_count . '" class="' . $this->options['item_classes']['list_container'] . '">';

        $in_list = true;

        echo '<div style="display:flex">'; // per-item container
            if ($this->options['subtitles_as_labels']) {
                //item marker
                echo '<div style="flex: 0.15" id ="' . $this->options['item_ids']['marker'] . $loop_count .  '" class="' . $this->options['item_classes']['marker'] . '">';
                    echo $item['level'] == 0 ? $item['marker'] : '';
                echo '</div>';
            }
            //item
            echo '<div style="flex: 2" id ="' . $this->options['item_ids']['list_item'] . $loop_count .  '" class="' . $this->options['item_classes']['list_item'] . '">';
                echo str_repeat($this->options['space'], $item['level']) . $item['text'];
            echo '</div>';
        echo '</div>'; // close per-item container

        $loop_count++;
    }

    /**
     * @param $item array
     * @param $loop_count int
     */
    private function handleTitleItem(array $item, int &$loop_count): void
    {
        if($item['level'] == 1) {
            echo '<h2 id ="'.$this->options['item_ids']['title']. $loop_count . '" class="'.$this->options['item_classes']['title'].'">';
            echo $item['text'];
            echo '</h2>';

            $loop_count++;
        }

        if($item['level'] == 2) {
            if ($this->options['subtitles_as_labels'])
                return;

            echo '<h3 id ="'.$this->options['item_ids']['title']. $loop_count . '" class="'.$this->options['item_classes']['title'].'">';
                echo $item['text'];
            echo '</h3>';

            $loop_count++;
        }
    }
}
