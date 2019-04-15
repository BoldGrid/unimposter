<?php
/**
 * Boldgrid Unimposter class.
 *
 * @package Boldgrid\Unimposter
 *
 * @version 1.0.0
 * @author BoldGrid <wpb@boldgrid.com>
 */
namespace Boldgrid\Unimposter;

/**
 * Boldgrid Unimposter class.
 *
 * This class handles undoing changes made by TypistTech/imposter.
 *
 * @since 1.0.0
 */
class unimposter {
	/**
	 * The imposter namespace.
	 *
	 * Defined in your composer.json file. Dynamically retrieved via this->getNamespace().
	 *
	 * @since 1.0.0
	 */
	private static $namespace;

	/**
	 * Get our namespace.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function getNamespace() {
		if ( ! empty( self::$namespace ) ) {
			return self::$namespace;
		}

		// This script is intended to be ran from the directory where your composer.json file is.
		$composerFilepath = getcwd() . '/composer.json';

		if ( ! file_exists( $composerFilepath ) ) {
			return self::$namespace;
		}

		$composerConfigs = file_get_contents( $composerFilepath );
		$composerConfigs = json_decode( $composerConfigs );

		self::$namespace = ! empty( $composerConfigs->extra->imposter->namespace ) ? $composerConfigs->extra->imposter->namespace : self::$namespace;

		return self::$namespace;
	}

	/**
	 * Print our Imposters.
	 *
	 * When running via the command line, this method prints the imposters found.
	 *
	 * @since 1.0.0
	 */
	public static function printImposters( $heading, $namespace ) {
		// Find imposters.
		$cmd = 'grep -FR "namespace ' . $namespace . '" vendor/*';
		exec( $cmd, $output );

		// Print imposters.
		print( "\n" );
		$output = empty( $output ) ? 'No imposters found.' : $output;
		print( $heading . "\n" );
		print_r( $output );
		print( "\n\n" );
	}


	/**
	 * Run.
	 *
	 * This is the main method of this class. It handles the unimpostering.
	 *
	 * @since 1.0.0
	 */
	public static function run() {
		$namespace = self::getNamespace();

		if ( empty( $namespace ) ) {
			return;
		}

		self::printImposters( 'BEFORE - Imposters found:', $namespace );

		// Fix imposters.
		$cmd = 'find vendor -type f -name "*.php" -print0 | xargs -0 sed -i \'s/namespace ' . str_replace( '\\', '\\\\', $namespace . '\\' ) . '/namespace /g\'';
		exec( $cmd );

		self::printImposters( 'AFTER - Imposters found:', $namespace );
	}
}