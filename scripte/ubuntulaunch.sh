#!/bin/bash
qemu-img create -f qcow2 Image.img 50G 
qemu-system-x86_64 -enable-kvm -cdrom ubuntu-24.04.3-live-server-amd64.iso -boot menu=on -drive file=Image.img -m 4G -smp 9 -vga virtio -display sdl,gl=on
