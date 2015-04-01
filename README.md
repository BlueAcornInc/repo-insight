Usage
=====

List available commands:
```repo-insight list```

Write list of beanstalk repositores to a CSV file (~/beanstalk_repos.csv)
```repo-insight beanstalk:list > ~/beanstalk_repos.csv```

Write list of beanstalk repositories as JSON (~/beanstalk_repos.json)
```repo-insight beanstalk:list > ~/beanstalk_repos.json```



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


