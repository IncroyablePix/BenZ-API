<?php
namespace SmallPHP\Controller;

require_once(__DIR__ . "/../utils/jwt.php");

use SmallPHP\CurrentJwt\TokenData;

abstract class RouteElement
{
    public abstract function get_name(): string;
    public abstract function matches(string $element): bool;
    public abstract function get_match_score(): int;
}

class StaticRouteElement extends RouteElement
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function matches(string $element): bool
    {
        return $element == $this->name;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_match_score(): int
    {
        return 2;
    }
}

class ParameterRouteElement extends RouteElement
{
    private string $type;
    private string $name;

    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function matches(string $element): bool
    {
        if(is_numeric($element) && $this->type == "int")
            return true;
        else if(is_string($element) && $this->type == "string")
            return true;

        return false;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_match_score(): int
    {
        return 1;
    }
}

class Route
{
    const URL_PARAM_REGEX = "{(int|string):([a-zA-Z0-9_]+)}";

    public array $parts;
    private $callback;

    public function __construct(array $parts, callable $method)
    {
        $this->parts = [];
        $this->callback = $method;

        $i = 0;

        foreach($parts as $part)
        {
            $matches = [];

            if(preg_match(self::URL_PARAM_REGEX, $part, $matches))
            {
                $this->parts[$i] = new ParameterRouteElement($matches[1], $matches[2]);
            }
            else
            {
                $this->parts[$i] = new StaticRouteElement($part);
            }

            $i ++;
        }
    }

    public function try_route(array $parts): int
    {
        $match_score = 0;

        if(count($parts) !== count($this->parts))
            return false;

        for($i = 0; $i < count($parts); $i ++)
        {
            $route_element = $this->parts[$i];
            if(!$route_element->matches($parts[$i]))
            {
                $match_score = 0;
                break;
            }
            else
            {
                $match_score += $route_element->get_match_score();
            }
        }

        return $match_score;
    }

    public function execute(array $parts, HttpResponse $response): void
    {
        $query_params = [];

        for($i = 0; $i < count($parts); $i ++)
        {
            $route_element = $this->parts[$i];
            $query_params[$route_element->get_name()] = $parts[$i];
        }
        call_user_func($this->callback, $query_params, $response);
    }
}

class HttpResponse
{
    public int $status_code = 404;
    public string $body = "";
    public array $headers = [];
}

abstract class Controller
{
    protected array $endpoint;
    protected ?TokenData $token_data;
    protected string $method;
    protected array $endpoints;

    public function __construct(array $endpoint, ?TokenData $token_data, string $method)
    {
        $this->endpoint = $endpoint;
        $this->token_data = $token_data;
        $this->method = $method;
        $this->endpoints = [
            "POST" => [],
            "GET" => [],
            "PUT" => [],
            "DELETE" => [],
            "PATCH" => [],
        ];
    }

    public function add_endpoint(string $method, string $route, callable $callback): void
    {
        $route_parts = preg_split("#/#", $route);
        array_unshift($route_parts, $this->get_name());
        $route_parts = array_filter($route_parts, function ($element) {
            return is_string($element) && "" !== trim($element);
        });

        $this->endpoints[strtoupper($method)][] = new Route($route_parts, $callback);
    }

    public abstract function get_name(): string;

    protected function get_body_from_json(): array
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    public function execute(): HttpResponse
    {
        $response = new HttpResponse();
        $parse_endpoints = $this->endpoints[$this->method];

        $filtered_endpoints = array_filter($parse_endpoints, function ($endpoint)
        {
            return $endpoint->try_route($this->endpoint) > 0;
        });

        $endpoint = array_reduce($filtered_endpoints, function ($carry, $item)
        {
            $score = $item->try_route($this->endpoint);
            if($score > $carry["score"])
            {
                $carry["score"] = $score;
                $carry["endpoint"] = $item;
            }

            return $carry;
        }, ["score" => 0, "endpoint" => null]);

        if($endpoint["endpoint"] !== null)
        {
            $endpoint["endpoint"]->execute($this->endpoint, $response);
        }
        // filter endpoints

        /*foreach($parse_endpoints as $endpoint)
        {

        }

        foreach($parse_endpoints as $ep)
        {
            if($ep->try_route($this->endpoint, $response))
            {
                $ep->execute($this->endpoint, $response);
                break;
            }
        }*/

        return $response;
    }

    protected final function token_id(): int
    {
        return $this->token_data === null ?
            -1 :
            $this->token_data->get_id();
    }
}
?>
