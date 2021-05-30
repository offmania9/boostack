<?php
/**
 * Boostack: Rest_Api_Abstract.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */

abstract class Rest_ApiAbstract
{

    protected $method = '';

    protected $endpoint = '';

    protected $verb = '';

    protected $args = Array();

    protected $content_type = "";

    protected $request = null;

    protected $file = null;

    protected $messageBag = null;

    protected $apiRequest = null;

    /**
     * Rest_Api_Abstract constructor.
     * @param $request
     * @throws Exception
     */
    public function __construct($requestedMethod)
    {
        $this->apiRequest = new Rest_ApiRequest();
        $this->messageBag = new MessageBag();
        $this->messageBag->error = false;
        $this->method = Request::getServerParam('REQUEST_METHOD');

        header("Access-Control-Allow-Orgin: *"); // Allow for CORS
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Content-Type: application/json");
        header("Access-Control-Allow-Headers: Content-Type, Origin, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        switch($this->method) {
            case 'DELETE':
            case 'POST':
                if (array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                    if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                        $this->file = parse_str(trim(file_get_contents("php://input")));
                        $this->file = Utils::sanitizeInput($this->file);
                        $this->request = Request::getQueryArray();
                    } else {
                        throw new Exception("Unexpected Header");
                    }
                }
                else{
                    #$this->file = trim(file_get_contents("php://input")); // in teoria sarebbe da eliminare, ma da Postman funziona solo in questo modo: effettuare delle prove da browser
                    $this->file = Request::getFilesArray();
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
                $this->_setErrorMessageObject('Invalid Method',StatusCodes::HTTP_METHOD_NOT_ALLOWED);
                break;
        }

        $this->args = explode('/', rtrim($requestedMethod, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && ! is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }

    }

    /**
     * @return string
     */
    public function processAPI()
    {
        try{
            #var_dump(Auth::getLastTry());
            if(!Utils::checkAcceptedTimeFromLastRequest(Auth::getLastTry())){
                throw new Exception_APITooManyRequests("Too much request. Wait some seconds");
            }
            if ((int)method_exists($this, $this->endpoint) > 0) {
                $this->trackRequest();
                $this->apiRequest->save();
                $this->messageBag->data = $this->{$this->endpoint}($this->args);
                $this->messageBag->code = StatusCodes::HTTP_OK;
            }
            else{
                throw new Exception_APINotFound("No Endpoint: " . $this->endpoint . ". The resource you requested doesn't exist. For more info, please refer to  ".Boostack::getInstance()->url."docs");
            }
        }
        catch (Exception_APITooManyRequests$e) {
            $this->_setErrorMessageObject($e->getMessage(),StatusCodes::HTTP_TOO_MANY_REQUEST);
        }
        catch (Exception_APINotFound $e) {
            $this->_setErrorMessageObject($e->getMessage(),StatusCodes::HTTP_NOT_FOUND);
        }
        catch (Exception $e) {
            $this->_setErrorMessageObject($e->getMessage(),StatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
        finally{
            Auth::impressLastTry();
            $this->trackRequest();
            $this->apiRequest->save();
        }
        header(StatusCodes::getHttpHeaderFor($this->messageBag->code));

        return $this->messageBag->toJSON();
    }

    private function _setErrorMessageObject($message,$code,$data = null){
        $this->messageBag->error = true;
        $this->messageBag->code = $code;
        $this->messageBag->message = $message;
        $this->messageBag->data = $data;
    }

    private function trackRequest() {
        $this->apiRequest->method = $this->method;
        $this->apiRequest->endpoint = $this->endpoint;
        $this->apiRequest->verb = $this->verb;
        $this->apiRequest->get_args = isset(Request::getQueryArray()["request"])?Request::getQueryArray()["request"]:"";
        $this->apiRequest->post_args = json_encode(Request::getPostArray());
        $this->apiRequest->file_args = json_encode($this->file);
        $this->apiRequest->remote_address = Utils::getIpAddress();
        $this->apiRequest->remote_user_agent = Utils::getUserAgent();
        $this->apiRequest->error = $this->messageBag->error?1:0;
        $this->apiRequest->code = $this->messageBag->code;
        $this->apiRequest->message = $this->messageBag->message;
        $this->apiRequest->output = json_encode($this->messageBag->data);
    }
}
?>