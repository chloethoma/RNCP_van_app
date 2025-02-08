import { Loader } from "lucide-react";

function Profile() {
  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-gray-800">
      <Loader size={50} className="animate-spin text-blue-500 mb-4" />
      <h1 className="text-2xl font-semibold">Page en cours de construction</h1>
      <p className="text-gray-600 mt-2">Revenez bientôt pour découvrir cette fonctionnalité ! 🚀</p>
    </div>
  );
}

export default Profile;
