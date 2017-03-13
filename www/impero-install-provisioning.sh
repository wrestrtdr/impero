#!/usr/bin/env bash
# this script should be loaded from https://impero.foobar.si/installer/[userhash]

# @REMOTE

# static user hash
USERHASH='userhash'

# randomly generate password
IMPEROPASSWORD=$(< /dev/urandom tr -dc _A-Z-a-z-0-9 | head -c${1:-40};)

# hash and store password
echo 'Hashing and password to filesystem'
HASHEDPASSWORD=$(openssl passwd -crypt $IMPEROPASSWORD)
echo $HASHEDPASSWORD > /tmp/impero.hashed.password

# add impero user, do not create home directory, set password, do not request name, room and phone
echo 'Creating user'
useradd impero --no-create-home -p $(cat /tmp/impero.hashed.password)

# remove hashed password from filesystem
echo 'Removing password from filesystem'
rm /tmp/impero.hashed.password

# add user to sudo group
echo 'Adding user to sudo group'
usermod -aG sudo impero

# change sudoers file
echo 'Adding user to sudoers'
echo '# User rules for impero' >> /etc/sudoers.d/100-impero
echo 'impero ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers.d/100-impero

# save plain password
echo 'Saving password to filesystem'
echo $IMPEROPASSWORD > /tmp/impero.password

# encrypt password
echo 'Encrypting password for supervisor'
openssl aes-256-cbc -a -in /tmp/impero.password -out /tmp/impero.encrypted.password
ENCRYPTEDPASSWORD=$(cat /tmp/impero.encrypted.password)

# delete encrypted and hashed password
echo 'Deleting plain and encrypted password'
rm /tmp/impero.password /tmp/impero.encrypted.password

# notify supervisor and create server
echo 'Notifying supervisor about new server, this can take a moment ...'
IMPEROHOSTNAME=$(cat /etc/hostname)
IMPEROURL='http://impero.foobar.si/api/installer/new-server/'$USERHASH'?hostname='$IMPEROHOSTNAME'&password='$ENCRYPTEDPASSWORD
curl -v -X POST $IMPEROURL

exit
return

# @T00D00 - set $REMOTEID variable

# @SUPERVISOR

# supervisor will catch request and resolve password
# this shouldn't take more than 5 seconds
# generate rsa key, 4096bit, without password
# @T00D00 - use password, it's stronger!
echo 'Generating rsa key'
ssh-keygen -b 4096 -t rsa -C 'impero@remote' -f /tmp/impero_remote -N ""

# transfer it to remote
echo 'Transfering rsa key'
echo 'Checking connection'
sshpass -p $IMPEROPASSWORD ssh-copy-id -p 22 $IMPEROUSER@remote

# try to connect
echo 'Checking connection'
ssh impero@remote

# @REMOTE

# during supervisor actions, we need to wait on remote.
# @T00D00 - wait for info, make request each 5 seconds
echo 'Waiting for supervisor to estamblish connection ...'
curl -X POST https://impero.foobar.si/api/installer/check-init/[userhash]/$REMOTEID/$IMPEROHASH

# change password and / or disable login with password
# change ssh config
# PermitRootLogin no / without-password

# notify user
echo "Connection successfully estamblished. You can check status in impero panel."