[![License](https://img.shields.io/github/license/razvanbackpack/clgparser)](https://github.com/razvanbackpack/clgparser/blob/main/LICENSE)
[![Version](https://img.shields.io/github/v/tag/razvanbackpack/clgparser)](https://github.com/razvanbackpack/clgparser/tags)

A simple php script that parses and displays changelog files.


Example:
``` 
# v0.1.0 

## added
- Reads changelog file
- Parses it
  - Headers and list items separately
    - List items support multiple indentations

## removed
- Old demo image, was dumb

## changed
- Old readme, was dumb


changelog.md
```

```
$ClgView = new ClgView('CHANGELOG.md');
$ClgView->parse();
```
![Output](assets/v010.png)
