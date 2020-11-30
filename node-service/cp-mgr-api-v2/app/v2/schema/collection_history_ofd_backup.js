/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('collection_history_ofd_backup', {
    history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    collection_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    collection_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    payment_mode: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    proof: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    reference_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    collected_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    collected_on: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    amount: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    data: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    discount: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    discount_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    ecash: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    due_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'collection_history_ofd_backup'
  });
};
