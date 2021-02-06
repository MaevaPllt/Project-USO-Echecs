<?php

namespace App\Controller;

class ChessGameController extends AbstractController
{
    public function rules()
    {
        return $this->twig->render('ChessGame/rules.html.twig');
    }
}
