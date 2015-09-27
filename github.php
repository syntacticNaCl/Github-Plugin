<?php
/*
 * Plugin Name: Github Display
 * Plugin URI:
 * Description: Plugin for wordpress that displays Github information.
 * Author: syntacticNaCl
 * Author URI: http://garrettrappaport.com
 * Version: 1.0
 * Text Domain: github-display
 */

namespace SyntacticNaCl\GitHub;

/**
 * Class GithubDisplay
 * @package SyntacticNaCl\GitHub
 */
class GithubDisplay {
	public static function load()
	{
		require_once 'vendor/autoload.php';

		// TODO make user dynamic

		register_activation_hook( __FILE__, array( __CLASS__, '_wp_activation' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, '_wp_deactivation' ) );
	}

	/**
	 * Method that constructs the data needed for display.
	 *
	 * @return array
	 */
	public static function display()
	{
		// get repos
		$repos = self::getRepos();

		$commits = array();
		foreach($repos as $repo => $url) {
			$commits[$repo] = self::getCommits($repo);
		}

		return $commits;
	}

	/**
	 * Method to get all of the repos for a specific user.
	 *
	 * @return array
	 */
	private static function getRepos()
	{
		$client = new \Github\Client();
		$repositories = $client->api('user')->repositories('syntacticNaCl');

		$repositoryNames = array();
		foreach ($repositories as $repository) {
			$name = $repository['name'];
			$repositoryNames[$name] = $repository['html_url'];
		}
		return $repositoryNames;
	}

	/**
	 * Method to get the commits for a specific repo.
	 *
	 * @param $repo
	 *
	 * @return mixed
	 */
	private static function getCommits($repo)
	{
		$client = new \Github\Client();
		$commits = $client->api('repo')->commits()->all('syntacticNaCl', $repo, array('sha' => 'master'));

		return $commits[0]['commit'];
	}

	/**
	 * Method to get github user information.
	 *
	 * @return mixed
	 */
	private static function getUser()
	{
		$client = new \Github\Client();
		$userInfo = $client->api('user')->show('syntacticNaCl');

		return $userInfo;
	}

	/**
	 * Method called when the plugin is activated.
	 */
	public static function _wp_activation()
	{
		flush_rewrite_rules();
	}

	/**
	 * Method called when the plugin is deactivated.
	 */
	public static function _wp_deactivation()
	{
		flush_rewrite_rules();
	}

}

GithubDisplay::load();