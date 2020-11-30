/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('bank_details', {
    bank_detail_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    account_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bank_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    account_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    account_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ifsc_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    branch_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    micr_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    currency_code: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'bank_details'
  });
};
