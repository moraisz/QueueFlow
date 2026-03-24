<?php

namespace Src\Infrastructure\Controllers;

use Src\Core\AbstractClasses\Controller;
use Src\Core\Response;

class HomeController extends Controller
{
    public function get(): Response
    {
        return $this->renderView('pages/home', [
            'message' => 'Bem-vindo!',
            'title' => 'Home',
        ]);
    }
}
