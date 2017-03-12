#!/usr/bin/env bash

# @REMOTE

# add impero user, do not create home directory, set password, do not request name, room and phone
# openssl passwd -crypt myPassword
echo 'Creating user impero'
adduser impero -M -p 0123456789012345678901234567890123456789 --gecos

# add user to sudo group
echo 'Adding user impero to sudo group'
usermod -aG sudo impero

# change sudoers file
echo 'Adding user to sudoers'
echo '# User rules for impero' >> /etc/sudoers.d/100-impero
echo 'impero ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers.d/100-impero

# @SUPERVISOR

# generate rsa key, 4096bit, without password
echo 'Generating rsa key'
ssh-keygen -b 4096 -t rsa -C 'impero@remote' -f /tmp/impero_remote -N ""

# transfer it to remote
echo 'Transfering rsa key'
echo 'Checking connection'
sshpass -p 0123456789012345678901234567890123456789 ssh-copy-id -p 8129 impero@remote

# try to connect
echo 'Checking connection'
ssh impero@remote

# @REMOTE
# change ssh config
# PermitRootLogin no / without-password