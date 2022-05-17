**Release File**
Create an empty file `<project_root>/releases/.last_release_deployed`

**Deploy Tracker Started**
```
cd {{release}}/deployment
yarn install axios
node deploy-tracker.js *<SLACK-TOKEN>* *<SLACK-CHANNEL-ID>* *<PROJECT-NAME-AND-ENV>* *<REPO-URL>* *<GITHUB-TOKEN>* $(cat ../../.last_release_deployed) {{sha}}
```

**Deploy Tracker Ended**
```
cd {{release}}/deployment
echo '{{sha}}' > ../../.last_release_deployed
node deploy-tracker.js *<SLACK-TOKEN>* *<SLACK-CHANNEL-ID>* *<PROJECT-NAME-AND-ENV>*
```
