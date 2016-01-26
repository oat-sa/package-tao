<?php

require_once "phing/Task.php";
include_once 'phing/types/FileSet.php';
include_once 'phing/types/Reference.php';
require_once 'phing/tasks/ext/git/GitBaseTask.php';

class GitDiffTask extends GitBaseTask {

	 private $nameOnly;

	 /**
	* Property name to set with output value from git-log
	* @var string
	*/
	private $outputProperty;

	public function setNameOnly($boolean){
		$this->nameOnly = $boolean;
	}


	public function setOutputProperty($prop)
	{
		$this->outputProperty = $prop;
	}

	public function main(){

		$client = $this->getGitClient(false, $this->getRepository());
		$command = $client->getCommand('diff');
		
		if (null !== $this->nameOnly) {
			$command->setOption('name-only');
		}

		$this->log('git-diff command: ' . $command->createCommandString(), Project::MSG_INFO);


		try {
			$output = $command->execute();
		} catch (Exception $e) {
			throw new BuildException('Task execution failed', $e);
		}

		if (null !== $this->outputProperty) {
			
			$this->project->setProperty($this->outputProperty, $output);
		}
		$this->log(
			sprintf('git-diff: commit diff for "%s" repository', $this->getRepository()),
				Project::MSG_INFO
			);
		$this->log('git-diff output: ' . trim($output), Project::MSG_INFO);
	}

}

