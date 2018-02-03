<?php

namespace Hgabka\LoggerBundle\Controller;

use Hgabka\LoggerBundle\Traits\LoggableTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoggableController extends Controller
{
    use LoggableTrait;
}
