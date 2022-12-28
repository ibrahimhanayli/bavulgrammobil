import { StyleSheet,  View } from 'react-native';
import { Text,Divider,Appbar,Button } from 'react-native-paper';
import React,{useState} from 'react';

//sayfalar
import KayitPage from './views/kayitPage';
import TeslimPage from './views/teslimPage';
import TakipPage from './views/takipPage';

function App() {
  //sayfa aktiflik durumları
  const [aktifekran,setAktifEkran] = useState(0);



  return (
    <>
    <Appbar.Header >
      <Appbar.Content style={{width:"100%",alignItems:"center"}} title="Bavulgram" />
    </Appbar.Header>
    <View style={styles.container}>

      <Text variant='headlineSmall'>Bavulgram olarak sizlere en iyi hizmeti sunmak için elimizden geleni yapıyoruz.</Text>

      <Divider style={{marginTop:2,marginBottom:2}}/>

      <Button style={{width:300}} mode="contained" onPress={()=>setAktifEkran(1)}>
        Kayıt Oluştur
      </Button>

          <Divider />
     
      <Button style={{width:300}} mode="contained" onPress={()=>setAktifEkran(2)}>
        Bavul Takip
      </Button>

          <Divider />

      <Button style={{width:300}} mode="contained" onPress={()=>setAktifEkran(3)} >
        Teslim
      </Button>
    
      {aktifekran == 1 && <KayitPage/> }
      {aktifekran == 3 && <TeslimPage/>}
      {aktifekran == 2 && <TakipPage/> }
    </View>


    </>
  );
}

export default App;

const styles = StyleSheet.create({
  container: {
    padding:5,
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
  }
});
