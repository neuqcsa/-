### pwn1:

出题失误，判断条件应该是：
```c
if(num1 > 10 || num2 <= 0x123456)
```
不然输入1 和0x123456即可getshell。
原意是通过 两数相乘，让其结果大于**1\*\*32** ，这样整数溢出，**高位舍去，剩下的等于0x123456**即可。
简单计算下即可得到：
2   0x100123456/2 
答案不唯一。

### pwn2：
整数溢出，输入-1，绕过检查:
```c
if(size > 16)
    {
        puts("Stack Overflow ???");
        return 0;
    }
```
read的count参数是无符号型，-1补码0xffffffff，对应最大的无符号数，导致溢出。

这里main函数里栈布局有点不同
函数开始时：
```
.text:080484D4 ; __unwind {
.text:080484D4                 lea     ecx, [esp+4]
.text:080484D8                 and     esp, 0FFFFFFF0h
.text:080484DB                 push    dword ptr [ecx-4]
.text:080484DE                 push    ebp
.text:080484DF                 mov     ebp, esp
.text:080484E1                 push    ecx
.text:080484E2                 sub     esp, 24h
```
返回时:
```
.text:08048549                 mov     ecx, [ebp+var_4]
.text:0804854C                 leave
.text:0804854D                 lea     esp, [ecx-4]
.text:08048550                 retn
```
根据ecx保存的栈地址，来复原esp，所以溢出的时候会溢出到ecx。详细的利用手法看这里:
http://dittozzz.top/2019/03/24/Securinets-CTF-Quals-2019-%E9%83%A8%E5%88%86pwn%E9%A2%98wp/#more
baby2

### re1:

动调修改标志寄存器，让流程走到这里来：
```
    GetDlgItemTextA(hDlg, 1000, String, 0xFFFF);
        if ( strlen(String) == 8 )
    {
      v7[0] = 90;
      v7[1] = 74;
      v7[2] = 83;
      v7[3] = 69;
      v7[4] = 67;
      v7[5] = 97;
      v7[6] = 78;
      v7[7] = 72;
      v7[8] = 51;
      v7[9] = 110;
      v7[10] = 103;
      sub_4010F0(v7, 0, 10);                    // 排序
```
通过调试得知sub_4010F0是将v7进行排序。

然后对输入的string的某些部分进行base64编码，然后进行比较
```
      v9[0] = String[5];
      v9[2] = String[7];
      v9[1] = String[6];
      v4 = Base64_Encode(v9, strlen(v9));
      memset(v9, 0, 0xFFFFu);
      v9[1] = String[3];
      v9[0] = String[2];
      v9[2] = String[4];
      v5 = Base64_Encode(v9, strlen(v9));
      if ( String[0] == v7[0] + 34
        && String[1] == v7[4]
        && 4 * String[2] - 141 == 3 * v7[2]
        && String[3] / 4 == 2 * (v7[7] / 9)
        && !strcmp(v4, "ak1w")
        && !strcmp(v5, "V1Ax") )
```
脚本如下：
```
v7=[0]*11
v7[0] = 90
v7[1] = 74
v7[2] = 83
v7[3] = 69
v7[4] = 67
v7[5] = 97
v7[6] = 78
v7[7] = 72
v7[8] = 51
v7[9] = 110
v7[10] = 103
v7.sort()


flag = [0]*8
flag[0] = v7[0]+34
flag[1] = v7[4]
flag[2] = (3*v7[2]+141)/4
flag[3] = 8*(v7[7]/9)
flag[5] = ord('j')
flag[6] = ord('M')
flag[7] = ord('p')
flag[2] = ord('W')
flag[3] = ord('P')
flag[4] = ord('1')
s=""
for i in flag:
    s+=chr(i)
print s
```

### re2

```
      if ( chr <= 96 || chr > 122 )
      {
        if ( chr > 64 && chr <= 90 )            // A-Z
          str2[v2] = (chr - 39 - key[v3++ % Key_len] + 97) % 26 + 97;
      }
      else                                      // a-z
      {
        str2[v2] = (chr - 39 - key[v3++ % Key_len] + 97) % 26 + 97;
      }
```
这里爆破一下即可：
```
import string
key = "ADSFKNDCLS"
target = "killshadow"
key_len = len(key)
key = key.lower()
flag=""
idx=0
while 1:
    if idx == len(target):
        break
    for i in string.uppercase:
        if chr((ord(i) - 39 - ord(key[idx%key_len]) + 97) % 26 + 97) == target[idx]:
            flag+=i
            idx+=1
            break
    
print flag
```
