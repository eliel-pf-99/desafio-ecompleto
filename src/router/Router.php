<?php

/**
 * Classe responsável por gerenciar e despachar rotas da aplicação.
 * Registra rotas para diferentes tipos de requisições HTTP
 * despachando a requisição para o controlador.
 */
class Router
{
    private array $routes = [];

    /**
     * Registra uma rota POST.
     * @param string $uri
     * @param callable $callback
     */
    public function post(string $uri, callable $callback): void
    {
        $this->addRoute('POST', $uri, $callback);
    }

    /**
     * Função adiciona uma rota ao array $routes
     * @param string $method
     * @param string $uri
     * @param callable $callback
     */
    private function addRoute(string $method, string $uri, callable $callback): void
    {
        $this->routes[$method][$uri] = $callback;
    }

    /**
     * Função responsavel por despachar a requisição para o controller
     */
    public function dispatch(): void
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method][$uri])) {
            $callback = $this->routes[$method][$uri];
            call_user_func($callback);
        } else {
            throw new Exception("404 - Rota não encontrada: {$uri}");
        }
    }
}