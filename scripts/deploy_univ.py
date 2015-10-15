#!/usr/bin/python
import os
import shutil
import fileinput
import sys

####################################################
## Define the attributes used to enrich your data
## You can pass them as arguments or replace the values
## on the line starting with ldap_attributes = [
#

ldap_attributes = []

if sys.argv and len(sys.argv) > 1 :
	for arg in sys.argv[1:]:
		ldap_attributes.append(arg)
else :
	ldap_attributes = ['eduPersonPrimaryAffiliation', 'supannEntiteAffectationPrincipale', 'supannEtuCursusAnnee', 'supannEtuSecteurDisciplinaire']
#
##
####################################################


## Initialize script
script_dir=os.path.dirname(os.path.abspath(__file__))
conf_dir=os.path.abspath(script_dir+'/../logstash')
build_dir=os.path.abspath(script_dir+'/../build')

## The computed values for replacement
attr = "'" + "', '".join(ldap_attributes) + "'"
attr_hash = ""
for attribute in ldap_attributes:
	attr_hash += "'"+attribute+"' => '"+attribute+"'\n"

# Create the build directory tree 
if os.path.isdir(build_dir):
	shutil.rmtree(build_dir)
os.mkdir(build_dir)
os.mkdir(build_dir+'/scripts')
os.mkdir(build_dir+'/logstash')

# Copy and personnalize the original files
if os.path.isdir(conf_dir):
	for conf_file in os.listdir(conf_dir):
		if os.path.isfile(conf_dir+"/"+conf_file):
			with open(build_dir+"/logstash/"+conf_file,'w+') as build_file:
				with open(conf_dir+"/"+conf_file) as orig_file:
					for line in orig_file:
						build_file.write(line.replace('__VOS_ATTRIBUTS__', attr).replace('__VOS_ATTRIBUTS_HASHES__', attr_hash))

else:
	print "Warning: the directory "+conf_dir+" doesn't exist"

if os.path.isdir(script_dir):
	for script_file in os.listdir(script_dir):
		if os.path.isfile(script_dir+"/"+script_file):
			with open(build_dir+"/scripts/"+script_file,'w+') as build_file:
				with open(script_dir+"/"+script_file) as orig_file:
					for line in orig_file:
						build_file.write(line.replace('__VOS_ATTRIBUTS__', attr).replace('__VOS_ATTRIBUTS_HASHES__', attr_hash))
else:
	print "Warning: the directory "+script_dir+" doesn't exist"

print "The modified files are avalaible in the build directory: "+build_dir
