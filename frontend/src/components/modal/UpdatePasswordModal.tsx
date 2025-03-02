import { useState } from "react";
import { motion } from "framer-motion";

interface Props {
  onConfirm: (currentPassword: string, newPassword: string) => void;
  onCancel: () => void;
}

function PasswordChangeModal ({ onConfirm, onCancel }: Props) {
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmNewPassword, setConfirmNewPassword] = useState("");
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = () => {
    if (!currentPassword || !newPassword || !confirmNewPassword) {
      setError("Tous les champs doivent être remplis.");
      return;
    }    
    
    if (newPassword !== confirmNewPassword) {
      setError("Les nouveaux mots de passe ne correspondent pas.");
      return;
    }
    setError(null);
    onConfirm(currentPassword, newPassword);
  };

  return (
    <div className="fixed inset-0 bg-black/30 backdrop-blur-md flex justify-center items-center z-40">
      <motion.div
        initial={{ scale: 0.8, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        exit={{ scale: 0.8, opacity: 0 }}
        className="bg-white p-6 rounded-lg shadow-lg w-80 text-center"
      >
        <h2 className="text-lg font-semibold mb-4">Modifier le mot de passe</h2>

        <div className="flex flex-col gap-3">
          <input
            type="password"
            placeholder="Mot de passe actuel"
            value={currentPassword}
            onChange={(e) => setCurrentPassword(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg"
          />
          <input
            type="password"
            placeholder="Nouveau mot de passe"
            value={newPassword}
            onChange={(e) => setNewPassword(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg"
          />
          <input
            type="password"
            placeholder="Confirmer le nouveau mot de passe"
            value={confirmNewPassword}
            onChange={(e) => setConfirmNewPassword(e.target.value)}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg"
          />
          {error && <p className="text-red text-sm">{error}</p>}
        </div>

        <div className="flex justify-between mt-4">
          <button
            onClick={onCancel}
            className="px-4 py-2 bg-light-grey rounded-lg hover:bg-light-grey-hover transition"
          >
            Annuler
          </button>
          <button
            onClick={handleSubmit}
            className="px-4 py-2 bg-red text-white rounded-lg hover:bg-red-hover transition"
          >
            Confirmer
          </button>
        </div>
      </motion.div>
    </div>
  );
};

export default PasswordChangeModal;
