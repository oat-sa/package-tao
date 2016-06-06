#!/bin/sh
diff -aurN old new -x .svn > patch.patch
