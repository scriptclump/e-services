/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('bank_info', {
    bank_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    bank_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ifsc: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    micr: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    branch: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    contact_phone: {
      type: "DOUBLE",
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    district: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    country: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '99'
    }
  }, {
    tableName: 'bank_info'
  });
};
