#!/bin/bash

    # Build the site
    echo "build start"
    jekyll build
    echo "build done"
    
    git add *
	git commit -a -m "Built site on `date +'%Y-%m-%d %H:%M:%S'`"
    
    # Push build_site
	git push origin build_site
    
    # Switch to gh-pages branch to sync it with build_site /_site
    git checkout gh-pages
    
    git checkout build_site -- _site
    cp -r _site/ .
    rm -rf _site
    git add *
    git commit -a -m "Site updated on `date +'%Y-%m-%d %H:%M:%S'`"
    git push origin gh-pages

	# Finally, switch back to the master branch and exit block
	git checkout master
    
    # purge sass cache folder in case it exists within the master branch.
    rm -rf .sass-cache
