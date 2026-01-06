<?php
namespace App\Helper;
use Illuminate\Support\Facades\Route;

class DzHelper
{
	public static function action() {
		$route = Route::current();
		if ($route) {
			// Try to get action from route action array
			$action = $route->getAction();
			if (isset($action['controller'])) {
				$chunks = explode("@", $action['controller']);
				return end($chunks) ?: 'index';
			}
			// Try to get method name
			if (method_exists($route, 'getActionMethod')) {
				$method = $route->getActionMethod();
				if ($method) {
					return $method;
				}
			}
		}
		// Fallback for older Laravel versions
		if (method_exists(Route::class, 'currentRouteAction')) {
			$action = Route::currentRouteAction();
			if ($action) {
				$chunks = explode("@", $action);
				return end($chunks) ?: 'index';
			}
		}
		return 'index';
    }
    
    public static function controller() {
		$route = Route::current();
		if ($route) {
			$action = $route->getAction();
			if (isset($action['controller'])) {
				$controller = $action['controller'];
				$chunks = explode("\\", $controller);
				$controllerName = explode("@", end($chunks));
				return $controllerName[0] ?? 'DashboardController';
			}
			// Try to get controller instance
			$controller = $route->getController();
			if ($controller) {
				$className = get_class($controller);
				$chunks = explode("\\", $className);
				return end($chunks);
			}
		}
		// Fallback for older Laravel versions
		if (method_exists(Route::class, 'currentRouteAction')) {
			$action = Route::currentRouteAction();
			if ($action) {
				$chunks = explode("\\", $action);
				$controller = explode("@", end($chunks));
				return $controller[0] ?? 'DashboardController';
			}
		}
		return 'DashboardController';
    }
	
}
