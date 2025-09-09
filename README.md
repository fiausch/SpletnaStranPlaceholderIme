# SpletnaStranPlaceholderIme

GRUVBOX COLOR SCHEME:
<img width="900" height="600" alt="image" src="https://github.com/user-attachments/assets/f260ed6f-0c47-4508-b17e-bb2d94f36baf" />

<img width="800" height="438" alt="image" src="https://github.com/user-attachments/assets/2de82b0f-7876-4d16-a59e-7ba9e96af1b9" />

izberita ka ceta, idealno dark mode. lahka pa tut light ce bols rata, u glavnem bo boljs met unikaten scheme ki lep zgleda






qemu-img create -f qcow2 Image.img 40G
qemu-system-x86_64 -enable-kvm -cdrom ubuntu-24.04.3-live-server-amd64.iso -boot menu=on -drive file=Image.img -m 4G cpu host -smp 4 -vga qxl
