Repo-Insight
============

repo-insight analyzes [beanstalk] hosted repositories and gathers their

* storage requirements
* last modification times
* branch count
* feature branch count
* feature branch status

it outputs statistics as CSV or JSON, and is useful for determing the number 
of active feature branches we have across all blueacorn repositories.


repo-insight is built on the Symfony Console component, the same as n98-magerun, 
and **serves as a good starting point for developing console applications**.


Usage
=====

Installation
------------

* Requires PHP 5.3+, libcurl


* **Manual** 
  * Download repo-insight.phar ( http://gitlab.dev/brice.burgess/repo-insight/raw/master/repo-insight.phar )
  * optional install system-wide ```chmod +x repo-insight.phar && mv repo-insight.phar /usr/local/bin/repo-insight```
  
* **composer**
  * 

* configure .repo-insight.yml
 


Configuration
-------------

repo-insight uses a YAML configuration file to store service credentials &c.

It first looks for a file named ```.repo-insight.yml``` in the CWD, and then
in your home/.repo-insight directory (```~/.repo-insight/.repo-insight.yml```).


you may specify the config file to use by passing the __--config-file__ option
to any command.



```
---

# ~/.repo-insight/.repo-insight.yml example

beanstalk_account: blueacorn
beanstalk_username: brice
beanstalk_token: bab46bb35f1f3744e50da40852ZZZZZZZZZ


# [LIVE]
# workfront_endpoint: https://blueacorn.attask-ondemand.com
# workfront_username:
# workfront_password:


# [SANDBOX]
workfront_endpoint: https://cl02.attasksandbox.com
workfront_username: toby+php@blueacorn.com
workfront_password: ...

```


Examples
--------


List available commands:
```repo-insight list```

Write list of beanstalk repositores to ~/beanstalk_repos.csv
```repo-insight beanstalk:list > ~/beanstalk_repos.csv```

Write list of beanstalk repositories, but as a JSON file
```repo-insight beanstalk:list json > ~/beanstalk_repos.json```

See arguments and options available to the beanstalk:feature-stats command
```repo-insight help beanstalk:feature-stats```


Get a list of all feature branches in the SIG repository, including task statistics
```repo-insight beanstalk:feature-list --stats git@blueacorn.git.beanstalkapp.com:/blueacorn/sig.git````


NOTE: We have not incorporated a progress meter. Executing --stats could take 
a VERY LONG time to complete all network requests. Be prepared...



Example Output
--------------

(last updated 2015-04-02)

See the CSV `beanstalk:feature-list --stats git@blueacorn.git.beanstalkapp.com:/blueacorn/sig.git` output of the SIG repository here:

  https://docs.google.com/a/blueacorn.com/spreadsheets/d/1Q5rXUDoMlppr8hyr5dQtnhbnBXYC0FmXUPUUayI5kIg/edit?usp=sharing
  
  
See the CSV `beanstalk:repo-list --stats` output of ALL repositories here:

  https://docs.google.com/a/blueacorn.com/spreadsheets/d/198FnmDtO3Zk21wx_fq8idmqTWpqQZ0jV1dKbcBAwv30/edit?usp=sharing


NOTE: repository size is in bytes.


Development
===========

Requirements
------------

* composer
* box (http://box-project.org/)



Release Process
---------------

(repo root)

* ```./release.sh <semver>```
  * tags working copy (git tag <semver>) && pushes tag to origin 
  * builds phar with box.phar
  * updates manifest.json


