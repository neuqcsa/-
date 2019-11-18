#include<stdio.h>
#include<stdlib.h>
void getshell()
{
    system("/bin/sh");
}
int main()
{
    unsigned int num1 = 0;
    unsigned int num2 = 0;
    scanf("%d %d",&num1,&num2);
    if(num1 > 10 || num2 < 0x123456)
        exit(-1);
    if(num1*num2 == 0x123456)
        getshell();
}