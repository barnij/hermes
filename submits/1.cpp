#include <iostream>
#include <string>

using namespace std;

int fib(int n)
{
if(n<2) return 1;
return fib(n-1)+fib(n-2);
}

int main()
{
    int a, b;
    cin>>a>>b;
    cout<<fib(30);


    return 0;
}
