import { useEffect, useState } from "react";
import { Check, LogOut, Pencil } from "lucide-react";
import Header from "../components/header/Header";
import { User } from "../types/user";
import {
  deleteUser,
  fetchUserByToken,
  updateUser,
} from "../services/api/apiRequests";
import ErrorMessage from "../components/ErrorMessage";
import SuccessMessage from "../components/SuccessMessage";
import IconButton from "../components/buttons/IconButton";
import { useNavigate } from "react-router";
import ConfirmationModal from "../components/modal/ConfirmationModal";

interface InfoRowProps {
  label: string;
  value: string;
  onSave: (value: string) => void;
  type?: string;
}

const MESSAGES = {
  ERROR_DEFAULT: "Une erreur est survenue",
  ERROR_FETCH_USER: "Erreur lors de a récupération de vos données",
  SUCCESS_UPDATE: "Mise à jour effectuée !",
  SUCCESS_DELETE: "Votre compte a bien été supprimé !",
};

function Profile() {
  const navigate = useNavigate();

  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const [user, setUser] = useState<User | null>(null);
  const [isModalOpen, setIsModalOpen] = useState(false);

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const fetchedUser = await fetchUserByToken();
        setUser(fetchedUser);
      } catch (error) {
        if (error instanceof Error) {
          setErrorMessage(error.message);
        } else {
          setErrorMessage(MESSAGES.ERROR_FETCH_USER);
        }
      }
    };

    fetchUser();
  }, [user]);

  const handleUpdate = async (field: keyof User, newValue: string) => {
    if (!user) return;

    try {
      const updatedUser = { ...user, [field]: newValue };
      await updateUser(updatedUser);
      setUser(updatedUser);
      setSuccessMessage(MESSAGES.SUCCESS_UPDATE);
    } catch (error) {
      if (error instanceof Error) {
        setErrorMessage(error.message);
      } else {
        setErrorMessage(MESSAGES.ERROR_DEFAULT);
      }
    }
  };

  const handleUpdatePassword = (newPassword: string) => {
    return newPassword;
  };

  const handleLogOut = () => {
    localStorage.removeItem("access_token");
    navigate("/login");
  };

  const handleDelete = async () => {
    try {
      await deleteUser();
      navigate("/login");
    } catch (error) {
      if (error instanceof Error) {
        setErrorMessage(error.message);
      } else {
        setErrorMessage(MESSAGES.ERROR_DEFAULT);
      }
    }
  };

  return (
    <div className="flex flex-col items-center w-full min-h-screen bg-light-grey font-default">
      <Header text={"Mon profil"} />

      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />
      <SuccessMessage
        successMessage={successMessage}
        setSuccessMessage={setSuccessMessage}
      />

      {user && (
        <div className="w-full flex flex-col items-center">
          {/* Profil Section */}
          {/* <div className="w-full bg-white p-3 flex flex-row items-center justify-between shadow-md">
            <img
              src={user.avatar}
              alt="avatar"
              className="w-20 h-20 rounded-full mx-5"
            />
            <div className="flex-1 text-center">
              <p className="text-lg font-semibold text-black">{user.spotNumber}</p>
              <p className="text-xs text-grey">spots enregistrés</p>
            </div>
            <div className="flex-1 text-center">
              <p className="text-lg font-semibold text-black">{user.friendNumber}</p>
              <p className="text-xs text-grey">amis dans ma commu</p>
            </div>
          </div> */}

          {/* Informations utilisateur */}
          <div className="w-full bg-white mt-4 p-4 shadow-md">
            <InfoRow
              label="Pseudo"
              value={user.pseudo}
              onSave={(newValue) => handleUpdate("pseudo", newValue)}
            />
            <InfoRow
              label="Adresse e-mail"
              value={user.email}
              onSave={(newValue) => handleUpdate("email", newValue)}
            />
            <InfoRow
              label="Mot de passe"
              value="••••••••"
              onSave={(newPassword) => handleUpdatePassword(newPassword)}
              type="password"
            />
          </div>

          {/* Déconnexion */}
          <div className="w-full flex justify-between items-center p-4 bg-white mt-4 shadow-md text-black">
            <p className="text-md font-semibold text-black">Déconnexion</p>
            <IconButton
              onClick={handleLogOut}
              icon={<LogOut size={20} color="black" />}
              color="white"
            />
          </div>

          {/* Suppression du compte */}
          <button
            onClick={() => setIsModalOpen(true)}
            className="w-11/12 mt-6 p-4 bg-red text-white text-lg font-bold rounded-lg hover:bg-red-hover"
          >
            Supprimer mon compte
          </button>

          {/* Modale de confirmation avant suppression du compte */}
          {isModalOpen && (
            <ConfirmationModal
              title="Êtes-vous sûr de vouloir supprimer votre compte ?"
              onConfirm={handleDelete}
              onCancel={() => setIsModalOpen(false)}
              confirmText="Oui, supprimer"
              cancelText="Annuler"
            />
          )}
        </div>
      )}
    </div>
  );
}

function InfoRow({ label, value, onSave, type = "text" }: InfoRowProps) {
  const [isEditing, setIsEditing] = useState<boolean>(false);
  const [inputValue, setInputValue] = useState<string>(value);

  useEffect(() => {
    if (!isEditing) {
      setInputValue(value);
    }
  }, [value, isEditing]);

  const handleSave = () => {
    onSave(inputValue);
    setIsEditing(false);
  };

  return (
    <div className="flex justify-between items-center py-2 border-b border-light-grey">
      <div className="flex-1">
        <p className="text-sm text-grey">{label}</p>
        {isEditing ? (
          <input
            type={type}
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            className="text-md font-semibold text-black border border-gray-300 px-2 py-1 rounded-md w-full"
          />
        ) : (
          <p className="text-md font-semibold text-black">{value}</p>
        )}
      </div>
      {isEditing ? (
        <IconButton
          onClick={handleSave}
          icon={<Check size={20} color="green" strokeWidth={3} />}
          color="white"
        />
      ) : (
        <IconButton
          onClick={() => setIsEditing(true)}
          icon={<Pencil size={20} color="black" />}
          color="white"
        />
      )}
    </div>
  );
}

export default Profile;
