<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/09/25
 * Time: 14:53
 */

namespace Apine\Utility;

use Apine\Core\Request;
use Apine\MVC\RedirectionView;
use Apine\MVC\URLHelper;
use Apine\XML\Parser;


class Routes
{
    /**
     * Safely redirect to another URI.
     *
     * @param string $a_request
     *
     * @return RedirectionView
     */
    public static function redirect($a_request)
    {
        $new_view = new RedirectionView();
        $new_view->set_header_rule('Location', $a_request);
        
        return $new_view;
    }
    
    /**
     * Redirect to another end point of the application
     * using a full query string
     *
     * @param string  $a_request
     * @param integer $a_protocol
     *
     * @return RedirectionView
     */
    public static function internalRedirect($a_request, $a_protocol = APINE_PROTOCOL_DEFAULT)
    {
        $new_view = new RedirectionView();
        $protocol = (isset(Request::server()['SERVER_PROTOCOL']) ? Request::server()['SERVER_PROTOCOL'] : 'HTTP/1.0');
        
        if (!empty(Request::get()['request']) && $a_request == Request::get()['request']) {
            $new_view->set_header_rule($protocol . ' 302 Moved Temporarily');
        }
        
        // Remove Trailing slash
        $request = trim($a_request, '/');
        
        $new_view->set_header_rule('Location: ' . URLHelper::path($request, $a_protocol));
        
        return $new_view;
    }
    
    /**
     * Export XML routes in a JSON Format
     *
     * @param string $file
     *
     * @return array
     */
    public static function exportToJson($file)
    {
        $xml_routes = new Parser();
        $xml_routes->load_from_file($file);
        $routes = array();
        
        foreach ($xml_routes->getElementByTagName('route') as $item) {
            if ($item->nodeType == XML_ELEMENT_NODE) {
                $nodes = array();
                $method = "";
                $request = "";
                
                foreach ($item->attributes as $attr) {
                    if ($attr->nodeType == XML_ATTRIBUTE_NODE) {
                        if ($attr->nodeName === 'method') {
                            $method = $attr->nodeValue;
                        } elseif ($attr->nodeName === 'args') {
                            $nodes['args'] = (bool)$attr->nodeValue;
                        } else {
                            $nodes[$attr->nodeName] = $attr->nodeValue;
                        }
                    }
                }
                
                if (method_exists($item, 'getElementsByTagName')) {
                    foreach ($item->getElementsByTagName('*') as $node) {
                        if ($node->nodeType == XML_ELEMENT_NODE) {
                            if ($node->nodeName === 'request') {
                                $request = $node->nodeValue;
                            } else {
                                $nodes[$node->nodeName] = $node->nodeValue;
                            }
                        }
                    }
                }
                
                $routes[$request][$method] = $nodes;
            }
        }
        
        return $routes;
    }
    
}