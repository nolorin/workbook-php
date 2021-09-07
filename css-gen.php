<?php
// Basic class for CSS styles
class style {
	protected array $attr = array();
	protected string $type = '';
	public string $name;
	public bool $verbose = FALSE;
	public function __construct( string $name, string $type = 'class' ) {
		$this->name = $name;
		$this->type( $type );
	}
	public function __toString(): string {
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
	public function type( string $type ) {
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

// Test
$style = new style( 'block', 'class' );
$style->set( 'color', 'red' );
$style->set( 'padding', '2px 2.5px' );
echo $style . PHP_EOL . PHP_EOL;
$style->clear( 'padding' );
$style->set( 'margin', '2px' );
$style->verbose = TRUE;
echo $style . PHP_EOL;
$style->set( 'border-radius', '10px' );
$style->type( 'id' );
$style->verbose = FALSE;
echo $style . PHP_EOL;

