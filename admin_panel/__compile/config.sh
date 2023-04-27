#!/bin/bash

# Do not include trailing slashes

project_directory='D:/projects/product/sailon/admin_panel'

path_to_closure_compiler='C:/closurecompiler'      # Location of compiler.jar on your system

path_to_js_temp="${project_directory}/__compile/.js-tmp"                           # Where to store tmp files created during combination/compression

path_to_js_source="${project_directory}/lib/js"
path_to_js_compiled="${project_directory}/__compile/output"

# This could also be an absolute URL
# It's only used when outputting to js.php
# Usually this will just be "/js/compiled"
web_path_to_js="__compile/output"