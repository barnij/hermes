#include <bits/stdc++.h>
#include <fstream>
#include <windows.h>
#include <string>
#include <ctime>
#include <stdio.h>
using namespace std;
ifstream odbierz;
ifstream poprawna;

bool sprawdz(string a, string b)
{
    if(a==b)return true;
    else return false;
}
string losuj()
{
    int ile = rand()%61+20;
    string wynik;
    for(int i = 0; i<ile; i++)
    {
        wynik.push_back(rand()%26+65);
    }
    return wynik;
}

int main(int argc, char *argv[])
{
    srand(time(NULL)+getpid());
    string id_usr = argv[2];
    string nr_zad = argv[1];
    string id_ans = argv[3];
    string kod = "\"";
    kod = kod + argv[4];
    kod = kod + "\"";
    string aaa = "\"playground\\";
    aaa = aaa + nr_zad;
    aaa = aaa + ".in\"";
    string aab = "playground\\";
    aab = aab + nr_zad;
    aab = aab + ".out";
    string bbb = losuj()+".out";
    //string bbb = "users/" + id_usr + "/out.out";
    string tworz = "interpreter.exe " + kod + " " + aaa + " > " + bbb;
    const char *usr = tworz.c_str();
    system(usr);
    odbierz.open(bbb.c_str());
    poprawna.open(aab.c_str());
    string a, b;
    getline(odbierz,a);
    getline(poprawna,b);
    bool flaga = sprawdz(a,b);

    if(flaga)
        cout<<2;
    else cout<<0;

    poprawna.close();
    odbierz.close();
    remove(bbb.c_str());
    return 0;
}
