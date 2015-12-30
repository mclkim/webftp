# webftp
webftp

```
git clone git@github.com:mclkim/webftp.git
```

composer update
```
cd webftp
composer update
```

git clone jstree && plupload 
```
git submodule update --init --recursive
```

or
```
rm -r public/plugins/jstree
git submodule add https://github.com/vakata/jstree.git public/plugins/jstree

rm -r public/plugins/plupload
git submodule add https://github.com/moxiecode/plupload.git public/plugins/plupload
```

