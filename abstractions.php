<?php
// Abstract data objects
abstract class dataNode {
	protected string $name;
	protected string $owner;
	protected $source;
	protected string $key;
	public function __construct( string $name, string $owner = NULL, $source = NULL, string $key = NULL ) {
		$this->name = $name;
		if( !empty( $owner ) ) {
			$this->owner = $owner; 
		}
		if( !empty( $source ) ) {
			$this->source = $source;
		}
		if( !empty( $key ) ) {
			$this->key = $key;
		}
	}
	public function __get( string $name ) {
		return $this->$name;
	}
	public function name( string $name ) {
		$this->name = $name;
	}
}
trait variables {
	private array $vars = array();
	public function __get( string $name ) {
		if( $name == 'vars' ) {
			return trigger_error( "Access denied for object property 'vars' for object with trait 'variables'", E_USER_ERROR );
		} else {
			return parent::__get( $name );
		}
	}
	public function set( string $name, $value ) {
		$this->vars[$name] = $value;
	}
	public function clear( string $name ) {
		unset( $this->vars[$name] );
	}
	public function get( string $name ) {
		return $this->vars[$name];
	}
}
trait dataControl {
	private array $vars = array();
	private array $casts = array();
	private array $access = array();
	public function __get( string $name ) {
		if( in_array( $name, [ 'vars', 'casts', 'access' ] ) ) {
			return trigger_error( "Access denied for object property 'vars' for object with trait 'variables'", E_USER_ERROR );
		} else {
			return parent::__get( $name );
		}
	}
	public function set_cast( string $name, $example ) {
		$this->casts[$name] = gettype( $example );
		return TRUE;
	}
	public function unset_cast( string $name ) {
		unset( $this->casts[$name] );
		return TRUE;
	}
	public function set( string $name, $value, string $cast = NULL, $access = NULL ): bool {
		if( !empty( $cast ) ) {
			if( !empty( $cast ) && !is_callable( $this->casts[$cast] ) ) {
				if( gettype( $value ) == $this->casts[$cast] ) {
					$this->vars[$name] = $value;
					$output = TRUE;
				} else {
					trigger_error( "Object property cannot be set for object with trait 'dataControl' because the property is not the correct data type", E_USER_NOTICE );
					$output = FALSE;
				}
			} else {
				if( $this->casts[$cast]( $value ) ) {
					$this->vars[$name] = $value;
					$output = TRUE;
				} else {
					trigger_error( "Object property cannot be set for object with trait 'dataControl' because the property cast function returned 'FALSE'", E_USER_NOTICE );
					$output = FALSE;
				}
			}
		} else {
			$this->vars[$name] = $value;
			$output = TRUE;
		}
		if( $output && !empty( $access ) ) {
			$this->access[$name] = $access;
		}
		return $output;
	}
	public function clear( string $name, $access_code = NULL ): bool {
		if( !empty( $this->access[$name] ) ) {
			if( $this->access[$name] == $access_code ) {
				unset( $this->vars[$name] );
				unset( $this->access[$name] );
			} else {
				trigger_error( "Object property '$name' cannot be deleted because access is denied", E_USER_NOTICE );
				return FALSE;
			}
		} else {
			unset( $this->vars[$name] );
			unset( $this->access[$name] );
			return TRUE;
		}
	}
	public function get( string $name, $access_code = NULL ) {
		if( !empty( $this->access[$name] ) ) {
			if( $this->access[$name] == $access_code ) {
				return $this->vars[$name];
			} else {
				trigger_error( "Object property '$name' cannot be retrieved because access is denied", E_USER_NOTICE );
				return NULL;
			}
		} else if( !empty( $this->vars[$name] ) ) {
			return $this->vars[$name];
		} else {
			return NULL;
		}
	}
}
interface dataIterable {
	public function add_item( $value, $key = NULL ): bool;
	public function sub_item( $key ): bool;
	public function filter( $value ): bool;
	public function get_item( $key );
	public function get_array(): array;
}

// Test 1
class example extends dataNode {
	public function test( $message ) {
		echo "Test Message: " . $message . PHP_EOL;
	}
}

// $wrong = new dataNode( 'object' ); // Returns error: PHP Fatal error:  Uncaught Error: Cannot instantiate abstract class dataNode

$right = new example( 'object', 'tester' );
var_dump( $right );
$right->test( 'It works, damn it!' );

echo PHP_EOL;

// Test 2
class datum extends dataNode {
	use variables;
}
$test2 = new datum( 'object' );
$test2->set( 'var1', 5 );
$test2->set( 'var2', 10 );
$test2->set( 'var3', 15 );
$test2->clear( 'var2' );
var_dump( $test2->get( 'var1') );
var_dump( $test2->get( 'var2') );
var_dump( $test2->get( 'var3') );
var_dump( $test2 );

echo PHP_EOL;

// Test 3
class uberDatum extends dataNode {
	use dataControl;
}
$test3 = new uberDatum( 'object' );
$test3->set_cast( 'number', 1234 );
$test3->set( 'var1', 5, 'number' );
$test3->set( 'var2', 10, NULL, 'UnAU21b' );
$test3->set( 'var3', '15', 'number' );
$test3->set( 'var4', 20 );
$test3->clear( 'var2' );
var_dump( $test3->get( 'var1') ); // Returns 5
var_dump( $test3->get( 'var2') ); // Returns NULL, triggers Custom Error: PHP Notice:  Object property 'var2' cannot be retrieved because access is denied
var_dump( $test3->get( 'var2', 'UnAU21b' ) ); // Returns 10
var_dump( $test3->get( 'var3') ); // Returns NULL
var_dump( $test3->get( 'var4') ); // Returns 20
var_dump( $test3 );

echo PHP_EOL;

// Test 4
class wrongImplement /*implements dataIterable*/ { // Returns error: PHP Fatal error:  Declaration of wrongImplement::sub_item($key) must be compatible with dataIterable::sub_item($key): bool
	public function add_item( $value, $key = NULL ): bool {
		return TRUE;
	}
	public function sub_item( $key ) { // Wrong return type
		return NULL;
	}
	public function filter( $value, $cast = NULL ): bool { // Args don't match up
		return FALSE;
	}
	// missing get_item method
	public function get_array() { // Wrong return cast
		return array();
	} 
}
