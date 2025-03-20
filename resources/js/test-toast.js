// Script de test pour les notifications
import { useToast } from 'vue-toastification';

export function testToast() {
  const toast = useToast();
  
  // Test de différents types de notifications
  toast.success("Ceci est un message de succès");
  setTimeout(() => {
    toast.error("Ceci est un message d'erreur");
  }, 1000);
  setTimeout(() => {
    toast.info("Ceci est un message d'information");
  }, 2000);
  setTimeout(() => {
    toast.warning("Ceci est un message d'avertissement");
  }, 3000);
  
  console.log("Tests de notifications lancés");
  return "Tests de notifications lancés";
}
