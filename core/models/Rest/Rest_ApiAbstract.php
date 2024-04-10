<?php
namespace Core\Models\Rest;
use Core\Models\Utils\Utils;
use Core\Models\Request;
use Core\Models\StatusCodes;
use Core\Models\Config;
use Core\Models\MessageBag;
use Core\Models\Auth;

/**
 * Boostack: Rest_Api_Abstract.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */

abstract class Rest_ApiAbstract
{

    protected static $outputNoLogged = false;

    protected $method = '';

    protected $endpoint = '';

    protected $verb = '';

    protected $args = array();

    protected $content_type = "";

    protected $request = null;

    protected $file = null;

    protected $messageBag = null;

    protected $apiRequest = null;

    /**
     * Constructor for the Rest_Api_Abstract class.
     *
     * @param $requestedMethod
     * @throws \Exception
     */
    public function __construct($requestedMethod)
    {
        Config::constraint("api_on");
        $this->apiRequest = new Rest_ApiRequest();
        $this->messageBag = new MessageBag();
        $this->messageBag->error = false;
        $this->method = Request::getServerParam('REQUEST_METHOD');

        // Allow for CORS
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Content-Type: application/json");
        header("Access-Control-Allow-Headers: Content-Type, Origin, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        switch ($this->method) {
            case 'DELETE':
            case 'POST':
                if (array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                    if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                        $fs = trim(file_get_contents("php://input"));
                        $f = null;
                        $this->file = parse_str($fs, $f);
                        $this->file = Request::sanitizeInput($this->file);
                        $this->request = Request::getQueryArray();
                    } else {
                        throw new \Exception("Unexpected Header");
                    }
                } else {
                    $this->file = empty(Request::getFilesArray()) ? trim(file_get_contents("php://input")) : Request::getFilesArray();
                    $this->request = Request::getPostArray();
                }
                break;
            case 'GET':
                $this->request = Request::getQueryArray();
                break;
            case 'OPTIONS':
                header(StatusCodes::getHttpHeaderFor(StatusCodes::HTTP_OK));
                die();
            default:
                $this->_setErrorMessageObject('Invalid Method', StatusCodes::HTTP_METHOD_NOT_ALLOWED);
                break;
        }

        $this->args = explode('/', rtrim($requestedMethod, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
    }

    /**
     * Process the API request.
     *
     * @return string
     */
    public function processAPI()
    {
        try {
            if (!Request::checkAcceptedTimeFromLastRequest(Auth::getLastTry())) {
                throw new \Core\Exception\Exception_APITooManyRequests("Too many requests. Please wait a few seconds.");
            }

            $methodBindings = [];
            $subclasses = [];
            $dir = Config::get("api_my_extended_classes_dir");
            $namespace = Config::get("api_my_extended_namespace");
            $declaredClasses = [];
            $this->getDirContents($dir, $declaredClasses);
        
            foreach ($declaredClasses as $class) {
                $classReflection = new \ReflectionClass($namespace.$class);
                if ($classReflection->isSubclassOf("\Core\Models\Rest\\".'Rest_ApiAbstract')) {
                    $subclasses[] = $class;
                }
            }

            foreach ($subclasses as $subclass) {
                $subclass = new \ReflectionClass($namespace.$subclass);
                $methods = $subclass->getMethods(\ReflectionMethod::IS_PROTECTED);
                foreach ($methods as $method) {
                    $methodBindings[$method->name] = $method->class;
                }
            }

            if (isset($methodBindings[$this->endpoint])) {
                $class = $methodBindings[$this->endpoint];
                $classInstance = new $class("");
                $this->trackRequest();
                $this->apiRequest->save();
                $this->messageBag->data = $classInstance->{$this->endpoint}($this->args);
                $this->messageBag->code = StatusCodes::HTTP_OK;
            } else {
                throw new \Core\Exception\Exception_APINotFound("No Endpoint: " . $this->endpoint . ". The resource you requested doesn't exist. For more info, please refer to the documentation.");
            }
        } catch (\Core\Exception\Exception_APITooManyRequests $e) {
            $this->_setErrorMessageObject("API Too many requests", StatusCodes::HTTP_TOO_MANY_REQUEST, $e->getMessage());
        } catch (\Core\Exception\Exception_APINotFound $e) {
            $this->_setErrorMessageObject("API not found", StatusCodes::HTTP_NOT_FOUND, $e->getMessage());
        } catch (\Exception $e) {
            $this->_setErrorMessageObject("Process API method error", StatusCodes::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        } finally {
            Auth::impressLastTry();
            $this->trackRequest();
            $this->apiRequest->save();
        }

        header(StatusCodes::getHttpHeaderFor($this->messageBag->code));

        return $this->messageBag->toJSON();
    }

    /**
     * Set error message object.
     *
     * @param $message
     * @param $code
     * @param null $data
     */
    private function _setErrorMessageObject($message, $code, $data = null)
    {
        $this->messageBag->error = true;
        $this->messageBag->code = $code;
        $this->messageBag->message = $message;
        $this->messageBag->data = $data;
    }

    /**
     * Track the API request details.
     */
    private function trackRequest()
    {
        $this->apiRequest->method = $this->method;
        $this->apiRequest->endpoint = $this->endpoint;
        $this->apiRequest->verb = $this->verb;
        $this->apiRequest->get_args = isset(Request::getQueryArray()["request"]) ? Request::getQueryArray()["request"] : "";
        $this->apiRequest->post_args = json_encode(Request::getPostArray());
        $this->apiRequest->file_args = json_encode($this->file);
        $this->apiRequest->remote_address = Request::getIpAddress();
        $this->apiRequest->remote_user_agent = Request::getUserAgent();
        $this->apiRequest->error = $this->messageBag->error ? 1 : 0;
        $this->apiRequest->code = $this->messageBag->code;
        $this->apiRequest->message = $this->messageBag->message;

        if (static::$outputNoLogged) {
            $this->apiRequest->output = "no-logged";
        } else {
            $this->apiRequest->output = json_encode($this->messageBag->data);
        }
    }

    /**
     * Apply constraints on the API method.
     *
     * @param $method
     * @param array|null $serverParams
     * @param array|null $headers
     * @param bool $fileIsJSON
     * @throws \Exception
     */
    protected function constraints($method, $currentUserIsLogged = false, ?array $headers = null, ?array $serverParams = null, bool $fileIsJSON = true)
    {
        if (strcasecmp($this->method, $method) !== 0) {
            throw new \Exception("Only accepts $method requests.");
        }

        if ($currentUserIsLogged && !Auth::isLoggedIn()) {
            throw new \Exception("Only accepts requests from already logged in user.");
        }

        if (!empty($serverParams)) {
            foreach ($serverParams as $key => $value) {
                if (!Request::hasServerParam($key)) {
                    throw new \Exception("Server param '$key' must be set.");
                }

                if ($value === "*") {
                    continue;
                }

                if (strcasecmp(Request::getServerParam($key), $value) !== 0) {
                    throw new \Exception("Server param '$key' must be set to: $value");
                }
            }
        }

        if (!empty($headers)) {
            foreach ($headers as $key => $value) {
                if ($key == "Content-Type") {
                    if (!Request::hasServerParam("CONTENT_TYPE")) {
                        throw new \Exception("Header param Content-Type must be set.");
                    }
                    if ($value === "*") {
                        continue;
                    }
                    if (strcasecmp(Request::getServerParam("CONTENT_TYPE"), $value) !== 0) {
                        throw new \Exception("Server param Content-Type must be set to: $value");
                    }
                    continue;
                }

                if (!Request::hasHeaderParam($key)) {
                    throw new \Exception("Header param '$key' must be set.");
                }

                if ($value === "*") {
                    continue;
                }

                if (strcasecmp(Request::getHeaderParam($key), $value) !== 0) {
                    throw new \Exception("Header param '$key' must be set to: $value");
                }
            }
        }

        if ($fileIsJSON) {
            if (!empty($this->file) && !Utils::isJson($this->file)) {
                throw new \Exception('Received content contained invalid JSON!');
            }
        }
    }

    /**
     * Recursively get all files in a directory.
     *
     * @param $dir
     * @param array $results
     * @return array
     */
    private function getDirContents($dir, &$results = [])
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = basename($path, ".php");
            } 
            elseif ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
            }
        }
        return $results;
    }
}
