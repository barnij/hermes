#include <iostream>
#include <string>

using namespace std;

int main()
{
    int a, b, Max = 0;
    cin >> a;
    for(int i = 0; i < a; i ++)
	{
		cin >> b;
		Max = max(Max, b);
	}
    cout << Max;
    return 0;
}
