import { useContext, useEffect, useState } from "react";
import { LogOut, Pencil } from "lucide-react";
import Header from "../components/headers/Header";
import { User } from "../types/user";
import {
  deleteUser,
  updateUser,
  updateUserPassword,
} from "../services/api/apiRequests";
import ErrorMessage from "../components/messages/ErrorMessage";
import SuccessMessage from "../components/messages/SuccessMessage";
import IconButton from "../components/buttons/IconButton";
import { useNavigate } from "react-router";
import ConfirmationModal from "../components/modal/ConfirmationModal";
import UpdatePasswordModal from "../components/modal/UpdatePasswordModal";
import UserContext from "../hooks/UserContext";
import { messages } from "../services/helpers/messagesHelper";
import Avatar from "../assets/avatar_cat.png";
import EditButton from "../components/buttons/EditButton";
import SaveButton from "../components/buttons/SaveButton";

interface InfoRowProps {
  label: string;
  value: string;
  onSave: (value: string) => void;
  type?: string;
}

interface InfoPasswordRowProps {
  label: string;
  value: string;
  onEditPassword: () => void;
}

function Profile() {
  const navigate = useNavigate();

  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const userContext = useContext(UserContext);
  const [isDeleteAccountModalOpen, setIsDeleteAccountModalOpen] =
    useState(false);
  const [isPasswordModalOpen, setIsPasswordModalOpen] = useState(false);

  if (!userContext) {
    return <div>Chargement...</div>; // ou rediriger
  }

  const { user, setUser } = userContext;

  const handleEdit = async (field: keyof User, newValue: string) => {
    if (!user || user[field] === newValue) return;

    const updatedUser = { ...user, [field]: newValue };

    try {
      await updateUser(updatedUser);
      setUser(updatedUser);
      setSuccessMessage(messages.success_update);
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  };

  const handleEditPassword = async (
    currentPassword: string,
    newPassword: string,
  ) => {
    try {
      const requestBody = { currentPassword, newPassword };
      await updateUserPassword(requestBody);
      setSuccessMessage(messages.success_update);
      setIsPasswordModalOpen(false);
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
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
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  };

  const handleEditPicture = () => {
    console.log("Edit picure");
  };

  return (
    <>
      <Header text="MON PROFIL" />
      <div className="flex flex-col items-center p-2 min-h-screen bg-light-grey font-default">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />
        <SuccessMessage
          successMessage={successMessage}
          setSuccessMessage={setSuccessMessage}
        />

        {user && (
          <div className="relative w-full flex flex-col items-center max-w-lg rounded-xl h-[calc(100vh-4rem-6rem)] md:p-4">
            {/* Profil section */}
            <div className="w-full bg-white p-4 flex flex-row items-center justify-between shadow-md rounded-xl">
              {/* Picture and edit button*/}
              <div className="relative">
                <img
                  src={user.picture || Avatar}
                  alt="avatar"
                  className="w-20 h-20 rounded-full mx-3 shadow-md"
                />
                <div className="absolute bottom-0 right-0">
                  <IconButton
                    onClick={handleEditPicture}
                    icon={<Pencil size={15} color="black" />}
                    color="white"
                    size="medium"
                  />
                </div>
              </div>
              {/* Extra informations */}
              <div className="flex-1 text-center">
                <p className="text-md font-semibold">10</p>
                <p className="text-xs text-grey">
                  <span className="block md:inline">spots</span>{" "}
                  <span className="block md:inline">enregistrés</span>
                </p>
              </div>
              <div className="flex-1 text-center">
                <p className="text-md font-semibold">20</p>
                <p className="text-xs text-grey">
                  <span className="block md:inline">amis dans</span>{" "}
                  <span className="block md:inline">ma commu</span>
                </p>
              </div>
            </div>

            {/* User informations */}
            <div className="w-full bg-white mt-4 p-4 shadow-md rounded-xl md:p-0 md:px-4">
              <InfoRow
                label="Pseudo"
                value={user.pseudo}
                onSave={(newValue) => handleEdit("pseudo", newValue)}
              />
              <InfoRow
                label="Adresse e-mail"
                value={user.email}
                onSave={(newValue) => handleEdit("email", newValue)}
              />
              <InfoPasswordRow
                label="Mot de passe"
                value="••••••••"
                onEditPassword={() => setIsPasswordModalOpen(true)}
              />
            </div>

            {/* Logout */}
            <div className="w-full flex justify-between items-center p-4 bg-white mt-4 shadow-md rounded-xl md:p-2">
              <p className="text-sm font-semibold">Déconnexion</p>
              <IconButton
                onClick={handleLogOut}
                icon={<LogOut size={16} color="black" />}
                color="white"
              />
            </div>

            {/* Delete account */}
            <button
              onClick={() => setIsDeleteAccountModalOpen(true)}
              className="w-9/12 mt-6 p-3 bg-red text-white text-sm font-bold rounded-lg cursor-pointer hover:bg-red-hover"
            >
              Supprimer mon compte
            </button>

            {/* Modal for confirmation delete account */}
            {isDeleteAccountModalOpen && (
              <ConfirmationModal
                title="Êtes-vous sûr de vouloir supprimer votre compte ?"
                onConfirm={handleDelete}
                onCancel={() => setIsDeleteAccountModalOpen(false)}
                confirmText="Oui, supprimer"
                cancelText="Annuler"
              />
            )}

            {/* Modal for update user password */}
            {isPasswordModalOpen && (
              <UpdatePasswordModal
                onConfirm={handleEditPassword}
                onCancel={() => setIsPasswordModalOpen(false)}
              />
            )}
          </div>
        )}
      </div>
    </>
  );
}

function InfoRow({ label, value, onSave }: InfoRowProps) {
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
    <div className="flex justify-between items-center py-2 border-b border-light-grey gap-4">
      <div className="flex-1">
        <p className="text-sm text-grey">{label}</p>
        {isEditing ? (
          <input
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            className="text-sm font-semibold border border-gray-300 px-2 py-1 rounded-md w-full"
          />
        ) : (
          <p className="text-sm font-semibold">{value}</p>
        )}
      </div>
      {isEditing ? (
        <SaveButton onClick={handleSave} />
      ) : (
        <EditButton onClick={() => setIsEditing(true)} />
      )}
    </div>
  );
}

function InfoPasswordRow({
  label,
  value,
  onEditPassword,
}: InfoPasswordRowProps) {
  return (
    <div className="flex justify-between items-center py-2 border-b border-light-grey">
      <div className="flex-1">
        <p className="text-sm text-grey">{label}</p>
        <p className="text-sm font-semibold">{value}</p>
      </div>
      <EditButton onClick={onEditPassword} />
    </div>
  );
}

export default Profile;
