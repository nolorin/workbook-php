<?php
// Basic HTML generating class
class html {
	public string $tag = 'div';
	protected array $attr = array();
	protected array $inner_html = array();
	public bool $short_tag = FALSE;
	public bool $close_tag = TRUE;
	public bool $verbose = TRUE;
	public function __construct( string $tag, $id = NULL ) {
		$this->tag = $tag;
		if( !empty( $id ) && ( is_string( $id ) || is_numeric( $id ) ) ) {
			$this->attr( 'id', $id );
		}
	}
	public function __toString(): string {
		$eol = $this->verbose ? PHP_EOL : '';
		$ind = $this->verbose ? "\t" : '';
		$output = '<' . $this->tag;
		if( !empty( $this->attr ) ) {
			$output .= ' ';
			foreach( $this->attr as $attr => $value ) {
				$output .= $attr . '=';
				$output .= !is_bool( $value ) ? '"' . (string) $value . '" ' : (string) $value;
			}
			$output = mb_substr( $output, 0, -1 );
		}
		if( !$this->short_tag && $this->close_tag ) {
			$output .= empty( $this->inner_html ) ? '>' : '>' . $eol;
			foreach( $this->inner_html as $child ) {
				$child_output = (string) $child;
				$output .= $ind . preg_replace( '/\n/', "\n" . $ind, $child_output ) . $eol;
			}
			$output .= '</' . $this->tag . '>';
		} else {
			$output .= $this->short_tag ? '/>' : '>';
		}
		return $output;
	}
	public function __get( string $prop ) {
		return $this->$prop;
	}
	public function attr( string $name, $value ): bool {
		if( !is_object( $value ) && !is_array( $value ) ) {
			$this->attr[$name] = $value;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function clear( string $name ): bool {
		unset( $this->attr[$name] );
		return TRUE;
	}
	public function append( $child, string $name = NULL ): bool {
		$key = $name ?? count( $this->inner_html );
		if( $child instanceof html || is_string( $child ) ) {
			$this->inner_html[$key] = $child;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function drop( string $key ) {
		unset( $this->inner_html[$key] );
		return TRUE;
	}
}

// Test
$html = new html( 'div' );
$html->attr( 'id', 'node-1' );
$html->attr( 'style', 'color:black' );
echo $html . PHP_EOL . PHP_EOL;
$html->short_tag = TRUE;
echo $html . PHP_EOL . PHP_EOL;
$html->short_tag = $html->close_tag = FALSE;
echo $html . PHP_EOL . PHP_EOL;

$child = new html( 'span' );
$child->attr( 'info', 'true' );
$html->append( $child, 'select' );
echo $html . PHP_EOL . PHP_EOL; // Doesn't print child because there is no closing tag
$html->close_tag = TRUE;
echo $html . PHP_EOL . PHP_EOL;
$html->verbose = FALSE;
echo $html . PHP_EOL . PHP_EOL;
$html->verbose = TRUE;
$html->clear( 'style' );
$html->append( 'Here is a text inner.' );
$html->drop( 'select' );
echo $html . PHP_EOL . PHP_EOL;

ob_start();
var_dump( $html );
$var_dump = ob_get_clean();
echo preg_replace( '/[\n\t\s+]/', '', $var_dump ) . PHP_EOL;

exit;
