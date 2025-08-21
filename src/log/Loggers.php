<?php

require_once __DIR__ . '\..\..\vendor\autoload.php';

/**Carrega o monolog */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/** Classe responsável por instanciar e compartilhar o Logger, usando o padrão Singleton */
class Loggers{
    /**Propriedades do Logger */
    private static ?Loggers $instance = null;   
    private Logger $logger;

    /**Funão Clone privado para evitar duplicação da instancia */
    private function __clone() {}

    /**Função que retorna somente uma instancia da classe. */
    private static function getInstance(): self {
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**Função estatica que retorna o Logger */
    public static function getLogger(): Logger {
        $instance = self::getInstance();

        if(!isset($instance->logger)){
            $logger = new Logger('App');
            $logger->pushHandler(new StreamHandler(__DIR__ . '\logs\app.log', Logger::WARNING));
            $instance->logger = $logger;
        }

        return $instance->logger;
    }

}
