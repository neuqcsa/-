#include<stdio.h>
#include<stdlib.h>
void getshell()
{
    system("/bin/sh");
}
int main()
{
    char buf[16];
    int size = 0;
    puts("input size");
    scanf("%d",&size);
    if(size > 16)
    {
        puts("Stack Overflow ???");
        return 0;
    }
    read(0,buf,size);
}