<?php
/**
 * A very simple dependency injection container.
 * 
 * PROBLEM:
 * 
 * There end up being a lot of objects that you need one of.
 * Examples: configuration strings, database and memcache
 * connections, table gateway instances, etc. Instantiating
 * many of them often requires one or more of the others.
 * Often each object requires a unique instantiation
 * algorthm. Other times many objects will use an almost
 * identical factory routine.
 * 
 * SOLUTION:
 * 
 * An object can contain the singleton properties.
 * Many of these properties will be set in the container:
 * 
 *     $container = new SiteContainer();
 *     $container->pdo_dsn = 'mysql:unix_socket=/tmp/mysql.sock;dbname=testdb';
 *     $container->pdo_user = 'user';
 *     $container->pdo_pass = 'password';
 *     $container->memcached_servers = array(
 *         array('mem1.domain.com', 11211, 33),
 *         array('mem2.domain.com', 11211, 67)
 *     );
 *     
 * Some properties can be created as needed.
 * Properties that have not been set in the object
 * will be created and stored for current and future use
 * using the object's createX method where X is the property requested.
 * 
 *     class SiteContainer extends Container {
 *         public function createDatabase() {
 *             return new PDO($this->pdo_dsn, $this->pdo_user, $this->pdo_pass);
 *         }
 *         
 *         public function createMemcache() {
 *             $memcached = new Memcached();
 *             $memcached->addServers($this->memcached_servers);
 *             return $memcached;
 *         }
 *     }
 *     
 *     $container->memcache->set('foo', 'bar');
 *     
 * In this last statement, the container's magic __get
 * method calls createMemcache(), stores the Memcached
 * object as its memcache property and finally
 * returns the Memcached object.
 * 
 * In cases where many slightly different objects require
 * the same factory method, they can be made by overriding
 * the createInstance method.
 * 
 *     class TableGatewayContainer extends Container {
 *         public function createInstance($table) {
 *             return new TableGateway($this->database, $table);
 *         }
 *     }
 */
class Container {
	
	/**
	 * If a property doesn't exist yet, create and store it.
	 * 
	 * @param string $property
	 * @return mixed
	 */
	public function __get($property) {
		try {
			$this->$property = $this->createInstance($property);
			return $this->$property;
		} catch (Exception $e) {
			trigger_error("Reference to unset property, '$property'", E_USER_NOTICE);
			return null;
		}
	}
	
	/**
	 * Calling undefined methods throws an exception,
	 * instead of E_ERRORing out of process.
	 * Mostly used by self::__get.
	 * 
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($method, $arguments) {
		throw new ErrorException('Call to undefined method '.get_class($this)."::$method()", E_ERROR);
	}
	
	/**
	 * Returns a new instance determined by the name.
	 * Subclasses should either override this method to return an
	 * appropriate instance of the name, or add createX methods,
	 * which will be called directly from this function.
	 * 
	 * @param string $property
	 */
	public function createInstance($property) {
		$method = 'create'.ucfirst($property);
		return $this->$method();
	}
}
