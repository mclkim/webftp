# webftp
webftp

```
git clone git@github.com:mclkim/webftp.git
cd webftp
composer update
```

```
git submodule update --init --recursive
```

```
rm -r public/plugins/jstree
git submodule add https://github.com/vakata/jstree.git public/plugins/jstree

rm -r public/plugins/plupload
git submodule add https://github.com/moxiecode/plupload.git public/plugins/plupload
```

```
cd public
mkdir _compile
```
