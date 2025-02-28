import { motion } from "framer-motion";

interface ModalProps {
  title: string;
  onConfirm: () => void;
  onCancel: () => void;
  confirmText?: string;
  cancelText?: string;
}

const Modal = ({
  title,
  onConfirm,
  onCancel,
  confirmText = "Confirmer",
  cancelText = "Annuler",
}: ModalProps) => {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
      <motion.div
        initial={{ scale: 0.8, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        exit={{ scale: 0.8, opacity: 0 }}
        className="bg-white p-6 rounded-lg shadow-lg w-80 text-center"
      >
        <h2 className="text-lg font-semibold mb-4">{title}</h2>
        <div className="flex justify-between mt-4">
          <button
            onClick={onCancel}
            className="px-4 py-2 bg-light-grey rounded-lg hover:bg-light-grey-hover transition"
          >
            {cancelText}
          </button>
          <button
            onClick={onConfirm}
            className="px-4 py-2 bg-red text-white rounded-lg hover:bg-red-hover transition"
          >
            {confirmText}
          </button>
        </div>
      </motion.div>
    </div>
  );
};

export default Modal;
