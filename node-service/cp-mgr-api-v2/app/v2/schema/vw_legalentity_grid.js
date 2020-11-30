/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_legalentity_grid', {
    Legal_Entity_ID: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    Code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Display_Name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Business_Name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    Warehouse: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Parent: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Contact_Name: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    FullName: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    Phone_No: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    Email: {
      type: DataTypes.STRING(96),
      allowNull: true
    },
    City: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    State_ID: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    StateName: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    GSTIN: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    1_N_Deposit: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    1_N_Credit_Limit: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0'
    },
    1_N_Available_Credit_Limit: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Is_Active: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    CustomAction: {
      type: DataTypes.STRING(209),
      allowNull: false,
      defaultValue: ''
    }
  }, {
    tableName: 'vw_legalentity_grid'
  });
};
