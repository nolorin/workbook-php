<?php
// Basic class for CSS styles
class style {
	protected array $attr = array();
	protected string $type = '';
	protected string $name;
	public bool $verbose = FALSE;
	public __construct( string $name, string $type = 'class' ) {
		$this->name = $name;
		$this->type( $type );
	}
	public __toString(): string {
		$eol = $this->verbose ? PHP_EOL : '';
		$ind = $this->verbose ? "\t" : '';
		$spa = $this->verbose ? ' ' : '';
		$output = $this->type . $this->name . $spa . '{' . $eol;
		foreach( $this->attr as $name => $value ) {
			$output .= $ind . $name . $spa . ':' . $spa . $value . ';' . $eol;
		}
		$output .= '}' . $eol;
		return $output;
	}
	public function __set( string $prop, $value ) {
		if( in_array( $value, [ 'name', 'type' ] ) ) {
			$this->$prop = $value;
			return TRUE;
		} else {
			return parent::__set( $prop, $value );
		}
	}
	public function unset( string $attr ) {
		if( in_array( $attr, [ 'name', 'type' ] ) ) {
			$this->$attr = '';
			return TRUE;
		} else {
			return parent::__unset( $attr );
		}
	}
	public function set( string $attr, $value ) {
		if( is_string( $value ) || is_numeric( $value ) ) {
			if( $this->attr[$attr] = $value ) {
				return TRUE;
			} else {
				return NULL;
			}
		} else {
			return FALSE;
		}
	}
	public function clear( string $attr ) {
		unset( $this->attr[$attr] );
		return TRUE;
	}
	pubilic function type( string $type ) {
		switch( $type ) {
			case 'class':
				$this->type = '.'; break;
			case 'id':
				$this->type = '#'; break;
			default:
				$this->type = '';
		}
		return TRUE;
	}
}
