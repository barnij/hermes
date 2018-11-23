#include< fstream >

using namespace std;
 int main()
 {
     ofstream file;
     file.open("test.txt");
     file << "test test test\n";
     file.close();
     return 0;
 }
