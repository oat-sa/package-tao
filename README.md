# deploy-test-package
Build a dedicated package and run the update

How to create a new deployment to test a feature

 - create a new branch from master that will start with test/myBranch

    `git checkout -b test/my-branch master`

 - edit build.properties, replace testDeploy with myBranch. Please notes that package.folder should not containts any special character because will be use as part as the DB name
 - when pushed a build will start on https://jenkins.taocloud.org/ use your github account to login
 - when the build is completed, it will trigger a build on http://deploy.taocloud.org/queue
 - (dev in progress) option tao.fresh.install by default is set to false but if set to true, it will trigger an resintall of the package instead of an update
