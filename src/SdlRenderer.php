<?php

namespace Phpokoban;

class SdlRenderer
{
    private $window;
    private $renderer;
    private $width = 0;
    private $height = 0;
    private $textureMap = [];

    /**
     * SdlRenderer constructor.
     *
     * @param Game   $game
     * @param string $title
     * @param int    $width  in pixels
     * @param int    $height in pixels
     */
    public function __construct(string $title, int $width, int $height)
    {
        \SDL_Init(SDL_INIT_VIDEO);
        $this->window = \SDL_CreateWindow(
            $title,
            SDL_WINDOWPOS_UNDEFINED, SDL_WINDOWPOS_UNDEFINED,
            $this->width = $width, $this->height = $height,
            SDL_WINDOW_SHOWN
        );
        $this->renderer = \SDL_CreateRenderer($this->window, -1, 0);
    }

    public function __destruct()
    {
        \SDL_DestroyRenderer($this->renderer);
        \SDL_DestroyWindow($this->window);
        \SDL_Quit();
    }

    public function addTextureFile(string $blockType, string $bmpFilePath): self
    {
        if (isset($this->textureMap[$blockType])) {
            throw new \Exception("Texture already exists for block '$blockType'.");
        }

        $image = \SDL_LoadBMP($bmpFilePath);
        if ($image === null) {
            throw new \Exception("Unable to load '$bmpFilePath'.");
        }

        // Set magenta as transparent color
        \SDL_SetColorKey($image, true, sdl_maprgb($image->format, 255, 0, 255));

        $this->textureMap[$blockType] = \SDL_CreateTextureFromSurface($this->renderer, $image);
        \SDL_FreeSurface($image);

        return $this;
    }

    public function run(Game $game)
    {
        // Compute graphical data
        $blockSize = (int) min(
            $this->width / $game->width(),
            $this->height / $game->height()
        );
        $rowOffset = (int) (($this->height - $game->height() * $blockSize) / 2);
        $colOffset = (int) (($this->width - $game->width() * $blockSize) / 2);

        // Events data
        $quit = false;
        $event = new \SDL_Event();
        $numKeys = 0;

        while (!$quit) {

            // Inputs polling
            $keyState = array_flip(\SDL_GetKeyboardState($numKeys, false));
            while (sdl_pollevent($event) !== 0) {
                switch ($event->type) {
                    case SDL_QUIT:
                        $quit = true;
                        break;
                    case SDL_KEYDOWN:
                        switch ($event->key->keysym->sym) {
                            case SDLK_UP:
                                $game->moveUp();
                                break;
                            case SDLK_DOWN:
                                $game->moveDown();
                                break;
                            case SDLK_LEFT:
                                $game->moveLeft();
                                break;
                            case SDLK_RIGHT:
                                $game->moveRight();
                                break;
                        }
                        break;
                }
            }

            //Clear screen in black
            \SDL_SetRenderDrawColor($this->renderer, 0, 0, 0, 0);
            \SDL_RenderClear($this->renderer);

            // Game background color
            \SDL_SetRenderDrawColor($this->renderer, 95, 150, 249, 255);

            // Game rendering
            for ($row = 0; $row < $game->height(); ++$row) {
                $y = $rowOffset + $row * $blockSize;
                for ($col = 0; $col < $game->width(); ++$col) {
                    $x = $colOffset + $col * $blockSize;
                    $rect = new \SDL_Rect($x, $y, $blockSize, $blockSize);
                    $blockType = $game->blockAt($row, $col);
                    \SDL_RenderFillRect($this->renderer, $rect);
                    if (isset($this->textureMap[$blockType])) {
                        \SDL_RenderCopy(
                            $this->renderer,
                            $this->textureMap[$blockType],
                            null,
                            $rect
                        );
                    }
                }
            }
            // Player
            if (isset($this->textureMap[Game::PLAYER_POS])) {
                $rect = new \SDL_Rect(
                    $colOffset + $game->player()->col() * $blockSize,
                    $rowOffset + $game->player()->row() * $blockSize,
                    $blockSize, $blockSize
                );
                \SDL_RenderCopy(
                    $this->renderer,
                    $this->textureMap[Game::PLAYER_POS],
                    null,
                    $rect
                );
            }

            // Screen refresh
            \SDL_RenderPresent($this->renderer);
            \SDL_Delay(10);
        }
        echo PHP_EOL;
    }
}
