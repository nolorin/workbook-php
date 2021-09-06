<?php
// Abstract data objects
abstract class dataNode {
	protected string $name;
	protected string owner;
	protected $source;
	protected string $key;
	public function __construct( string $name, string $owner = NULL, $source = NULL, string $key = NULL ) {
		$this->name = $name;
		$this->name = $owner;
		$this->name = $source;
		$this->name = $key;
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
	public function set( string $name, $value, string $cast = NULL, $access = NULL ): bool {
		if( !empty( $cast ) ) {
			if( !is_callable( $this->casts[$cast] ) ) {
				if( gettype( $value ) == gettype( $this->casts[$cast] ) ) {
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
			$output = FALSE;
		}
		if( $output && !empty( $access ) ) {
			$this->access[$name] = $access;
		}
		return $output;
	}
	public function clear( string $name, $access_code = NULL ): bool {
		if( !empty( $this->access[$name] ) ) {
			if( $this->access[$name]( $access_code ) ) {
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
			if( $this->access[$name]( $access_code ) ) {
				return $this->vars[$name];
			} else {
				trigger_error( "Object property '$name' cannot be retrieved because access is denied", E_USER_NOTICE );
				return NULL;
			}
		} else {
			return $this->vars[$name];
		}
	}
}
interface iterable {
	public function add_item( $value, $key = NULL ): bool;
	public function sub_item( $key ): bool;
	public function filter( $value ): bool;
	public function get_item( $key );
	public function get_array(): array;
}
