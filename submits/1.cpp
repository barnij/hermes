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
    cout<<fib(10);

    int tab[3] = {1,2,3};
    tab[-70] = 90;


    return 0;
}
