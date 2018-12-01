# PhpOkoban

<img src="doc/phpokoban.gif?raw=true" width="250" align="right" alt="PhpOkoban screenshot">

A [Sokoban](https://fr.wikipedia.org/wiki/Sokoban)-like game written in Php/SDL.


## Requirements

* Php 7.2
* [phpsdl extension](https://github.com/Ponup/phpsdl)

## Install

```shell
git clone git@github.com:b-viguier/PhpOkoban.git
cd PhpOkoban
composer install
```

## Playing

```shell
./bin/phpokoban
```

Or write your own level as a text file,
using [XSB file format](http://www.sokobano.de/wiki/index.php?title=Level_format).
```shell
./bin/phpokoban <file-name>
```

You can win or lose, the game will never finish.
You will have to restart the executable for a new game.