/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('logistics_partners', {
    lp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    lp_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    lp_legal_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address_1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    address_2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    country: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    phone: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    email: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    website: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    files: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    full_service: {
      type: DataTypes.ENUM('true','false'),
      allowNull: true
    },
    for_service: {
      type: DataTypes.ENUM('true','false'),
      allowNull: true
    },
    cod_service: {
      type: DataTypes.ENUM('true','false'),
      allowNull: true
    },
    api_username: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    api_password: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    api_apikey: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'logistics_partners'
  });
};
