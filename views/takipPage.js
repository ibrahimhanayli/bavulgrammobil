import { StyleSheet, View, ScrollView, ToastAndroid } from 'react-native';
import { Text,Divider,TextInput,Button } from 'react-native-paper';
import React from 'react';

export default function TakipPage() {
  const [bkod,setBkod] = React.useState('');

 
  const sorgulaBtn = async() => {
    fetch('https://bavulgram-backend.onrender.com/takip/'+bkod) //bavul takip isteği
    .then((response) => response.text())  //isteği metin olarak al
    .then((data) => { //gelen veriye yapılacak işlemler
      ToastAndroid.show(data, ToastAndroid.LONG); //toast mesajı göster
    })
  }

  return (
    <ScrollView>
    
    <View style={styles.container}>
      <Divider style={{marginTop:10,marginBottom:10,width:300}}/>

      <Text variant='headlineSmall'>Bavul Takip</Text>

      <Divider style={{marginTop:10,marginBottom:10,width:100}}/>

      <TextInput
        mode='outlined'
        label="Bavul Kodu"
        style={{width:300}}
        value={bkod}
        onChangeText={(b)=> setBkod(b)}
      />

      <Divider style={{marginTop:10,marginBottom:10,width:300}}/>

      <Button style={{width:200}} mode="contained" onPress={() => sorgulaBtn()}>
        Sorgula
      </Button>
    </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    padding:5,
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
  },
});
